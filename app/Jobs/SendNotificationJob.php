<?php

namespace App\Jobs;

use App\Events\AdminNotification;
use App\Events\EmployeeNotification;
use App\Events\PrivateNotification;
use App\Events\PublicNotification;
use App\Events\UserNotification;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle()
    {
        try {
            $notification = Notification::create([
                'type' => $this->data['type'],
                'category' => $this->data['category'],
                'priority' => $this->data['priority'],
                'title' => $this->data['title'],
                'message' => $this->data['message'],
                'from_user_id' => $this->data['from_user_id'],
                'to_user_id' => $this->data['to_user_id'],
                'status' => $this->data['status'],
                'goto_id' => $this->data['goto_id'],
                'goto_route' => $this->data['goto_route'],
                'expires_at' => $this->data['expires_at'],
            ]);

            $eventClass = match ($this->data['type']) {
                'public' => PublicNotification::class,
                'private' => PrivateNotification::class,
                'user' => UserNotification::class,
                'employee' => EmployeeNotification::class,
                'admin' => AdminNotification::class,
                default => null,
            };

            if ($eventClass) {
                broadcast(new $eventClass($this->data))->toOthers();
            }

            Log::info('Tạo và gửi thông báo thành công', [
                'notification_id' => $notification->id,
                'title' => $this->data['title'],
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý SendNotificationJob: ' . $e->getMessage(), [
                'data' => $this->data,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
