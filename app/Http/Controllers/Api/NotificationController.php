<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function getNotifications(int $userId, Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $category = $request->input('category');
        $notifications = $this->notificationService->getNotificationsByUser($userId, $perPage, $category);
        return response()->json([
            'success' => true,
            'data' => $notifications->items(),
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
        ]);
    }

    public function getUnreadCount(int $userId)
    {
        $notifications = $this->notificationService->getUnreadNotifications($userId)
            ->load('fromUser');

        $notifications->transform(function ($notification) {
            $notification->from_user_name = $notification->fromUser->name ?? null;
            unset($notification->fromUser);
            return $notification;
        });
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }

    public function markAsRead(Request $request, $id)
    {
        $userId = $request->input('user_id');
        $notification = Notification::findOrFail($id);

        if ($notification->to_user_id !== $userId) {
            Log::info('notification', [$notification, $userId]);

            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notification->status = 'read';
        $notification->read_at = now();
        $notification->save();

        return response()->json(['message' => 'Notification marked as read'], 200);
    }

    public function getOrderDetailsFromNotification($notificationId, Request $request)
    {
        $userId = $request->input('user_id');
        $notification = Notification::where('id', $notificationId)
            ->where('to_user_id', $userId)
            ->firstOrFail();

        if ($notification->category !== 'order' || !$notification->goto_id) {
            return response()->json(['message' => 'Invalid notification for order details'], 400);
        }

        $order = Order::findOrFail($notification->goto_id);
        return response()->json([
            'success' => true,
            'order' => $order,
            'notification' => $notification,
        ]);
    }
}
