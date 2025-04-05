<?php

namespace App\Services;

use App\Models\Slide;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class SlideService
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
    public function processSlideData(Request $request)
    {
        $formattedData = [
            '_token' => $request->input('_token'),
            'title' => $request->input('title'),
            'description' => $request->input('content'),
            'category_id' => $request->input('category_id'),
            'display_locations' => $request->input('display_locations'),
            'is_active' => $request->input('is_active', '1'),
        ];

        return $formattedData;
    }

    /**
     * Tạo slide mới và lưu vào database
     */
    public function createSlide(Request $request)
    {
        $this->user = auth()->user();

        try {
            $this->user = auth()->user();
            DB::beginTransaction();

            // Xử lý dữ liệu slide
            $formattedData = $this->processSlideData($request);

            // 1. Tạo slide mới
            $slide = Slide::create([
                'title' => $formattedData['title'],
                'description' => $formattedData['description'],
                'category_id' => $formattedData['category_id'],
                'display_locations' => $formattedData['display_locations'],
            ]);

            // 2. Lưu ảnh chính vào bảng slide_images
            if ($request->hasFile('image_primary')) {
                $thumbnailPath = $this->uploadImage($request->file('image_primary'), 'slides/thumbnails');
                if ($thumbnailPath) {
                    $slide->images()->create([
                        'slide_id' => $slide->id,
                        'image' => $thumbnailPath,
                        'is_primary' => 1,
                    ]);
                }
            }

            // 3. Lưu danh sách ảnh album vào bảng slide_images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePath = $this->uploadImage($image, 'slides/gallery');
                    $slide->images()->create([
                        'slide_id' => $slide->id,
                        'image' => $imagePath,
                        'is_primary' => false,
                    ]);
                }
            }

            // Commit transaction khi thành công
            DB::commit();

            // Gửi thông báo khi tạo slide mới
            $dataNotification = [
                'title' => 'New Slide',
                'message' => $this->user->name . ' đã tạo slide mới!',
                'from_user_id' => $this->user->id,
                'to_user_id' => null,
                'type' => 'slides',
                'status' => 'unread',
                'goto_id' => $slide->id,
            ];
            $this->NotificationService->sendAdmin($dataNotification);

            return $slide;
        } catch (\Exception $e) {
            // Rollback nếu có lỗi xảy ra
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Hàm upload ảnh
     */
    public function uploadImage($image, $folder = 'slides')
    {
        if (!$image) {
            return null;
        }

        $filename = time() . '_' . md5($image->getClientOriginalName()) . '.' . $image->getClientOriginalExtension();
        $image->storeAs($folder, $filename, 'public');

        return '/storage/' . $folder . '/' . $filename;
    }
}
