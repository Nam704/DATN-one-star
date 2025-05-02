<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('admin-notifications')];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->data['id'] ?? null,
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'from_user_id' => $this->data['from_user_id'] ?? null,
            'to_user_id' => $this->data['to_user_id'] ?? null,
            'category' => $this->data['category'] ?? 'system',
            'priority' => $this->data['priority'] ?? 'medium',
            'status' => $this->data['status'] ?? 'unread',
            'goto_id' => $this->data['goto_id'] ?? null,
            'goto_route' => $this->data['goto_route'] ?? 'client.dashboard',
            'expires_at' => $this->data['expires_at'] ?? null,
            'created_at' => $this->data['created_at'] ?? now()->toDateTimeString(),
        ];
    }
}
