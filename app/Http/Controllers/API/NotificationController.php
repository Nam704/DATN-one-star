<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
        $notifications = $this->notificationService->getUnreadNotifications($userId);
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }
}
