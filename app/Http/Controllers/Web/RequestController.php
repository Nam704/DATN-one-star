<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute_value;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Blog;
use App\Models\RequestModel;
use App\Services\BlogService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    protected $blogService;       // ← dòng mới

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
                // Mặc định original là mảng rỗng
                $original = [];

                // Nếu có model_id (update/delete/restore), lấy bản ghi gốc
                if ($req->model_id) {
                    $class = '\\App\\Models\\' . Str::studly($req->model_type);
                    if (class_exists($class)) {
                        // Với soft-deleted cần withTrashed nếu muốn so sánh cả bản đã xoá
                        $instance = $class::withTrashed()->find($req->model_id);
                        if ($instance) {
                            $original = $instance->toArray();
                        }
                    }
                }

                // Gán thêm thuộc tính original vào mỗi request
                $req->original = $original;

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
                $payload['status'] = 'active';
                Category::create($payload);
                break;

            case 'update':
                if ($pendingRequest->model_id) {
                    $category = Category::findOrFail($pendingRequest->model_id);
                    $payload['status'] = 'active';
                    $category->update($payload);
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
                $payload['status'] = 'active';
                Attribute::create($payload);
                break;

            case 'update':
                if ($pendingRequest->model_id) {
                    $attribute = Attribute::findOrFail($pendingRequest->model_id);
                    $payload['status'] = 'active';
                    $attribute->update($payload);
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
}
