<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getUnreadCount(int $userId)
    {
        $notifications = $this->notificationService->getUnreadNotifications($userId)
            ->load('fromUser'); // Giả sử có quan hệ fromUser trong model Notification

        // Lọc chỉ lấy tên từ fromUser
        $notifications->transform(function ($notification) {
            $notification->from_user_name = $notification->fromUser->name ?? null; // Lấy tên hoặc null nếu fromUser không tồn tại
            unset($notification->fromUser); // Loại bỏ thuộc tính fromUser nếu không cần
            return $notification;
        });
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->status = 'read';
        $notification->save();
        // dd($notification);

        return response()->json(['message' => 'Notification marked as read'], 200);
    }
}
