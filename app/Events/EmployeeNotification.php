<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeNotification implements ShouldBroadcast
{
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function broadcastOn(): array
    {
        return [new PrivateChannel('employee')];
    }
    public function broadcastWith()
    {
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'from_user_id' => $this->data['from_user_id'],
            'status' => $this->data['status'],
        ];
    }
}
