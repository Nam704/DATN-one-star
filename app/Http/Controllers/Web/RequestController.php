<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute_value;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Blog;
use App\Models\CategoryBlog;
use App\Models\RequestModel;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }
    public function index()
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $requests = RequestModel::where('status', 'pending')
            ->with('employee')
            ->latest()
            ->get()
            ->map(function ($req) {
                $original = [];
                if ($req->model_id) {
                    $class = '\\App\\Models\\' . Str::studly($req->model_type);
                    if (class_exists($class)) {
                        $instance = $class::withTrashed()->find($req->model_id);
                        if ($instance) {
                            $original = $instance->toArray();
                        }
                    }
                }

                $payload = $req->payload;

                // Xử lý blog
                if ($req->model_type === 'blog') {
                    // original: thêm tên chuyên mục
                    if (!empty($original['category_id'])) {
                        $cat = CategoryBlog::withTrashed()
                            ->find($original['category_id']);
                        $original['category_name'] = $cat->name ?? null;
                    } else {
                        $original['category_name'] = null;
                    }

                    // payload: thêm tên chuyên mục
                    if (!empty($payload['category_id'])) {
                        $cat2 = CategoryBlog::withTrashed()
                            ->find($payload['category_id']);
                        $payload['category_name'] = $cat2->name ?? null;
                    } else {
                        $payload['category_name'] = null;
                    }
                    $payload['slug'] = $payload['slug']     ?? $original['slug']       ?? null;
                    $payload['published_at'] = $payload['published_at']
                        ?? $original['published_at'] ?? null;
                }
                //Xử lý attribute_value
                if ($req->model_type === 'attribute_value') {
                    // original: thêm tên attribute
                    if (!empty($original['id_attribute'])) {
                        $attr = Attribute::withTrashed()
                            ->find($original['id_attribute']);
                        $original['attribute_name'] = $attr->name ?? null;
                    } else {
                        $original['attribute_name'] = null;
                    }

                    // payload: thêm tên attribute
                    if (!empty($payload['id_attribute'])) {
                        $attr2 = Attribute::withTrashed()
                            ->find($payload['id_attribute']);
                        $payload['attribute_name'] = $attr2->name ?? null;
                    } else {
                        $payload['attribute_name'] = null;
                    }
                }

                // Xử lý category
                if ($req->model_type === 'category') {
                    // parent cũ (original_data) nếu cần
                    if (!empty($original['id_parent'])) {
                        $p = Category::withTrashed()->find($original['id_parent']);
                        $original['parent_name'] = $p ? $p->name : null;
                        $req->setAttribute('original_data', $original);
                    }
                    // parent mới (payload)
                    if (!empty($payload['id_parent']) && $payload['id_parent'] !== 0) {
                        $p2 = Category::withTrashed()->find($payload['id_parent']);
                        $payload['parent_name'] = $p2 ? $p2->name : null;
                    } else {
                        $payload['parent_name'] = null;
                    }
                }

                $req->setAttribute('original_data', $original);
                $req->setAttribute('payload_data',  $payload);

                return $req;
            });


        return view('admin.approve.index', compact('requests'));
    }


    public function approve(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền thực hiện hành động này.');
        }

        $messages = [
            'request_ids.required' => 'Vui lòng chọn ít nhất một yêu cầu để phê duyệt.',
            'request_ids.array'    => 'Dữ liệu yêu cầu không hợp lệ.',
            'request_ids.*.exists' => 'Yêu cầu được chọn không tồn tại hoặc đã được xử lý.',
        ];

        $request->validate([
            'request_ids'   => 'required|array',
            'request_ids.*' => 'exists:request_model,id'
        ], $messages);

        $successCount  = 0;
        $failedCount   = 0;
        $failedReasons = [];

        foreach ($request->request_ids as $requestId) {
            $pendingRequest = RequestModel::where('id', $requestId)
                ->where('status', 'pending')
                ->first();

            if (!$pendingRequest) {
                Log::warning("Yêu cầu ID {$requestId} không tìm thấy hoặc không ở trạng thái chờ phê duyệt.");
                $failedCount++;
                $failedReasons[] = "Yêu cầu ID {$requestId}: Không tìm thấy hoặc không ở trạng thái chờ phê duyệt.";
                continue;
            }

            try {
                $modelType = $pendingRequest->model_type;
                $action    = $pendingRequest->action;

                switch ($modelType) {
                    case 'brand':
                        $this->processBrand($action, $pendingRequest);
                        break;

                    case 'category':
                        $this->processCategory($action, $pendingRequest);
                        break;

                    case 'attribute':
                        $this->processAttribute($action, $pendingRequest);
                        break;

                    case 'attribute_value':
                        $this->processAttributeValue($action, $pendingRequest);
                        break;
                    case 'blog':
                        $this->processBlog($action, $pendingRequest);
                        break;

                    default:
                        throw new \Exception("Loại mô hình '{$modelType}' không được hỗ trợ.");
                }

                $pendingRequest->update([
                    'status'      => 'approved',
                    'admin_id'    => auth()->id(),
                    'approved_at' => Carbon::now(),
                ]);

                Log::info("Quản trị viên ID " . auth()->id() . " đã phê duyệt yêu cầu {$pendingRequest->id} (hành động: {$action}, mô hình: {$modelType}).");
                $successCount++;
            } catch (\Exception $e) {
                $pendingRequest->update(['status' => 'rejected']);
                Log::error("Không thể phê duyệt yêu cầu {$pendingRequest->id}: " . $e->getMessage());
                $failedCount++;
                $failedReasons[] = "Yêu cầu ID {$requestId}: Lỗi - " . $e->getMessage();
            }
        }

        $messageParts = [];
        if ($successCount > 0) {
            $messageParts[] = "{$successCount} yêu cầu đã được phê duyệt thành công";
        }
        if ($failedCount > 0) {
            $messageParts[] = "{$failedCount} yêu cầu không thể phê duyệt";
        }
        $message = implode(' và ', $messageParts);
        if (!empty($failedReasons)) {
            $message .= '. Lý do: ' . implode('; ', $failedReasons);
        }

        return redirect()->route('admin.requests.index')
            ->with('success', $message);
    }

    protected function processBrand(string $action, RequestModel $pendingRequest)
    {
        $payload = $pendingRequest->payload;
        switch ($action) {
            case 'create':
                $payload['status'] = 'active';
                Brand::create($payload);
                break;

            case 'update':
                if ($pendingRequest->model_id) {
                    $brand = Brand::findOrFail($pendingRequest->model_id);
                    $payload['status'] = 'active';
                    $brand->update($payload);
                }
                break;

            case 'delete':
                if ($pendingRequest->model_id) {
                    Brand::findOrFail($pendingRequest->model_id)->delete();
                }
                break;

            case 'restore':
                if ($pendingRequest->model_id) {
                    $brand = Brand::withTrashed()->findOrFail($pendingRequest->model_id);
                    if ($brand->trashed()) {
                        $brand->restore();
                    }
                }
                break;

            default:
                throw new \Exception("Hành động '{$action}' không hợp lệ cho thương hiệu.");
        }
    }

    protected function processCategory(string $action, RequestModel $pendingRequest)
    {
        $payload = $pendingRequest->payload;

        // Nếu id_parent không tồn tại hoặc null, mặc định gán 0
        if (!isset($payload['id_parent']) || $payload['id_parent'] === null) {
            $payload['id_parent'] = 0;
        }

        switch ($action) {
            case 'create':
                // lúc tạo mới thì mặc định active
                $payload['status'] = 'active';
                Category::create($payload);
                break;

            case 'update':
                // update đúng payload, không ép status
                if ($pendingRequest->model_id) {
                    $category = Category::findOrFail($pendingRequest->model_id);
                    $category->update($payload);
                }
                break;

            case 'toggle_status':
                // riêng xử lý bật/tắt trạng thái
                if ($pendingRequest->model_id) {
                    $category = Category::findOrFail($pendingRequest->model_id);
                    $newStatus = $payload['status']
                        ?? ($category->status === 'active' ? 'inactive' : 'active');
                    $category->update(['status' => $newStatus]);
                }
                break;

            case 'delete':
                if ($pendingRequest->model_id) {
                    Category::findOrFail($pendingRequest->model_id)->delete();
                }
                break;

            case 'restore':
                if ($pendingRequest->model_id) {
                    $category = Category::withTrashed()->findOrFail($pendingRequest->model_id);
                    if ($category->trashed()) {
                        $category->restore();
                    }
                }
                break;

            default:
                throw new \Exception("Hành động '{$action}' không hợp lệ cho danh mục.");
        }
    }


    protected function processAttribute(string $action, RequestModel $pendingRequest)
    {
        $payload = $pendingRequest->payload;

        switch ($action) {
            case 'create':
                // luôn khởi tạo active
                $payload['status'] = 'active';
                Attribute::create($payload);
                break;

            case 'update':
                // chỉ update đúng payload, không ép status
                if ($pendingRequest->model_id) {
                    $attribute = Attribute::findOrFail($pendingRequest->model_id);
                    $attribute->update($payload);
                }
                break;

            case 'toggle_status':
                // xử lý bật / tắt riêng
                if ($pendingRequest->model_id) {
                    $attribute = Attribute::findOrFail($pendingRequest->model_id);
                    $newStatus = $payload['status']
                        ?? ($attribute->status === 'active' ? 'inactive' : 'active');
                    $attribute->update(['status' => $newStatus]);
                }
                break;

            case 'delete':
                if ($pendingRequest->model_id) {
                    Attribute::findOrFail($pendingRequest->model_id)->delete();
                }
                break;

            case 'restore':
                if ($pendingRequest->model_id) {
                    $attribute = Attribute::withTrashed()->findOrFail($pendingRequest->model_id);
                    if ($attribute->trashed()) {
                        $attribute->restore();
                    }
                }
                break;

            default:
                throw new \Exception("Hành động '{$action}' không hợp lệ cho thuộc tính.");
        }
    }

    protected function processAttributeValue(string $action, RequestModel $pendingRequest)
    {
        $payload = $pendingRequest->payload;
        switch ($action) {
            case 'create':
                $payload['status'] = 'active';
                Attribute_value::create($payload);
                break;

            case 'update':
                if ($pendingRequest->model_id) {
                    $attributeValue = Attribute_value::findOrFail($pendingRequest->model_id);
                    $attributeValue->update($payload);
                }
                break;

            case 'delete':
                if ($pendingRequest->model_id) {
                    Attribute_value::findOrFail($pendingRequest->model_id)->delete();
                }
                break;

            case 'restore':
                if ($pendingRequest->model_id) {
                    $attributeValue = Attribute_value::withTrashed()->findOrFail($pendingRequest->model_id);
                    if ($attributeValue->trashed()) {
                        $attributeValue->restore();
                    }
                }
                break;

            case 'toggle_status':
                if ($pendingRequest->model_id) {
                    $attributeValue = Attribute_value::findOrFail($pendingRequest->model_id);
                    $newStatus = $payload['status'] ?? ($attributeValue->status === 'active' ? 'inactive' : 'active');
                    $attributeValue->update(['status' => $newStatus]);
                }
                break;

            default:
                throw new \Exception("Hành động '{$action}' không hợp lệ cho giá trị thuộc tính.");
        }
    }
    /**
     * Xử lý các yêu cầu liên quan đến Blog
     */
    protected function processBlog(string $action, RequestModel $req)
    {
        $payload = (array) $req->payload;

        switch ($action) {
            case 'create':
                // Tạo mới bài viết
                $blog = Blog::create([
                    'title'       => $payload['title'],
                    'slug'        => $payload['slug'],
                    'content'     => $payload['content'],
                    'category_id' => $payload['category_id'],
                    'thumbnail'   => $payload['thumbnail'] ?? null,
                    'status'      => $payload['status'],
                    'author_id'   => $req->employee_id,
                ]);
                // nếu có tags
                if (!empty($payload['tags'])) {
                    $blog->tags()->sync($payload['tags']);
                }
                break;

            case 'update':
                $blog = Blog::findOrFail($req->model_id);
                $blog->update([
                    'title'       => $payload['title'],
                    'slug'        => $payload['slug'],
                    'content'     => $payload['content'],
                    'category_id' => $payload['category_id'],
                    'thumbnail'   => $payload['thumbnail'] ?? $blog->thumbnail,
                    'status'      => $payload['status'],
                ]);
                if (isset($payload['tags'])) {
                    $blog->tags()->sync($payload['tags']);
                }
                break;

            case 'delete':
                Blog::findOrFail($req->model_id)->delete();
                break;

            case 'restore':
                $b = Blog::withTrashed()->findOrFail($req->model_id);
                if ($b->trashed()) {
                    $b->restore();
                }
                break;

            default:
                throw new \Exception("Hành động '{$action}' không hợp lệ cho Blog.");
        }
    }
}
