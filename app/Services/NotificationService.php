<?php

namespace App\Services;

use App\Events\ImportNotificationSent;
use App\Models\Notification;

class NotificationService
{
    public function __construct()
    {
        // Constructor logic
    }
    function sendImport($importData, $user)
    {
        broadcast(new ImportNotificationSent($importData, $user));
    }
    public function createNotification(array $data)
    {
        return Notification::create([
            'type' => $data['type'], // Loại thông báo: user, employee, admin
            'title' => $data['title'], // Tiêu đề
            'message' => $data['message'], // Nội dung
            'from_user_id' => $data['from_user_id'] ?? null, // ID người gửi (nullable)
            'to_user_id' => $data['to_user_id'], // ID người nhận
            'status' => 'unread', // Mặc định là chưa đọc
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId, int $userId)
    {
        // Lấy thông báo thuộc về user hiện tại
        $notification = Notification::where('id', $notificationId)
            ->where('to_user_id', $userId) // Chỉ kiểm tra thông báo của user này
            ->first();

        if (!$notification) {
            throw new \Exception("Thông báo không tồn tại hoặc không thuộc về người dùng.");
        }

        $notification->status = 'read';
        $notification->read_at = now();

        return $notification->save();
    }

    /**
     * Get all notifications for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNotificationsByUser(int $userId)
    {
        return Notification::where('to_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get unread notifications for a specific user.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUnreadNotifications(int $userId)
    {
        return Notification::where('to_user_id', $userId)
            ->where('status', 'unread')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Delete a notification by ID.
     *
     * @param int $notificationId
     * @return bool
     */
    public function deleteNotification(int $notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        return $notification->delete();
    }
}
