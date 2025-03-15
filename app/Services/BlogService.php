<?php

namespace App\Services;

use App\Models\Blog;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class BlogService
{
    protected $NotificationService;
    protected $user;

    public function __construct(NotificationService $NotificationService,)
    {
        $this->NotificationService = $NotificationService;
    }

    /**
     * Xử lý và định dạng dữ liệu bài viết từ request
     */
    public function processBlogData(Request $request)
    {
        $formattedData = [
            '_token' => $request->input('_token'),
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'content' => $request->input('content'),
            'category_id' => $request->input('category_id'),
            'tags' => $request->input('name', []), // Lấy danh sách tag (nếu có)
            'thumbnail' => $request->file('thumbnail'),
            'status' => $request->input('status', 'published'),
        ];

        // Nếu có ảnh bài viết, tải lên và lấy đường dẫn
        if ($request->hasFile('thumbnail')) {
            $formattedData['thumbnail'] = $this->uploadImage($request->file('thumbnail'), 'blogs/thumbnails');
        }

        return $formattedData;
    }

    /**
     * Tạo sản phẩm và lưu dữ liệu vào database
     */
    public function createBlog(Request $request)
    {
        try {
            $this->user = auth()->user();
            DB::beginTransaction();

            // Xử lý dữ liệu bài viết
            $formattedData = $this->processBlogData($request);

            // 1. Tạo bài viết
            $blog = Blog::create([
                'title' => $formattedData['title'],
                'slug' => $formattedData['slug'],
                'content' => $formattedData['content'],
                'category_id' => $formattedData['category_id'],
                'thumbnail' => $formattedData['thumbnail'],
                'status' => $formattedData['status'],
                'author_id' => $this->user->id,
            ]);

            // 2. Gắn tag vào bài viết
            if (!empty($formattedData['tags'])) {
                $blog->tags()->sync($formattedData['tags']);
            }

            DB::commit();

            // 3. Gửi thông báo
            $dataNotification = [
                'title' => 'New Blog Post',
                'message' => $this->user->name . ' đã tạo bài viết mới!',
                'from_user_id' => $this->user->id,
                'to_user_id' => null,
                'type' => 'blogs',
                'status' => 'unread',
                'goto_id' => $blog->id,
            ];
            $this->NotificationService->sendAdmin($dataNotification);

            return $blog;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function uploadImage($image, $folder = 'products')
    {
        if (!$image) {
            return null;
        }

        // Tạo tên tệp dựa trên thời gian và hash của tên gốc
        $filename = time() . '_' . md5($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();

        // Lưu ảnh vào thư mục được chỉ định với tên tệp đã tạo
        $image->storeAs($folder, $filename, 'public');

        // Trả về đường dẫn hình ảnh
        return '/storage/' . $folder . '/' . $filename;
    }
}