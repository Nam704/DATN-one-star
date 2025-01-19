<?php

namespace App\Services;

use App\Events\ImportNotificationSent;
use App\Events\PrivateNotification;
use App\Events\PublicNotification;
use App\Models\Notification;
use App\Models\User;
use GuzzleHttp\Exception\TooManyRedirectsException;
use Illuminate\Support\Facades\Log;
use Illuminate\Broadcasting\PendingBroadcast;

class NotificationService
{

    public function __construct()
    {
        // Constructor logic
    }
    function sendImport($importData, $user)
    {
        broadcast(new ImportNotificationSent($importData, $user))->toOthers();
    }
    public function sendPublic($data)
    {
        $this->createNotification([
            'type' => 'public',
            'title' => $data['title'],
            'message' => $data['message'],
            'from_user_id' => $data['from_user_id'],
            'to_user_id' => $data['to_user_id'],
        ]);
        broadcast(new PublicNotification($data));
    }
    public function sendPrivate($data)
    {
        // Lọc người dùng có vai trò admin và employee
        $recipients = User::whereHas('role', function ($query) {
            $query->whereIn('name', ['admin', 'employee']);
        })->get();
        // dd($recipients);
        // Tạo thông báo cho từng người nhận
        foreach ($recipients as $recipient) {
            $this->createNotification([
                'type' => 'private',
                'title' => $data['title'],
                'message' => $data['message'],
                'from_user_id' => $data['from_user_id'],
                'to_user_id' => $recipient->id,
                'status' => 'unread',
            ]);
        }

        // Gửi thông báo qua broadcasting

        broadcast(new PrivateNotification($data))->toOthers();
        Log::info('Broadcast sent with toOthers', [
            'data' => $data,
            'user_id' => auth()->id(), // Người phát sự kiện
        ]);
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


    public function getNotificationsByUser(int $userId)
    {
        return Notification::where('to_user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function getUnreadNotifications(int $userId)
    {
        return Notification::where('to_user_id', $userId)
            ->where('status', 'unread')
            ->orderBy('created_at', 'desc')
            ->get();
    }


    public function deleteNotification(int $notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        return $notification->delete();
    }
}
