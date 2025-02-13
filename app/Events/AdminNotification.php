<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('admin'),
        ];
    }
    public function broadcastWith()
    {
        $from_user_name = User::where('id', $this->data['from_user_id'])->first()->name;
        return [
            'title' => $this->data['title'],
            'message' => $this->data['message'],
            'from_user_id' => $this->data['from_user_id'],
            'status' => $this->data['status'],
            'created_at' => $this->data['created_at'],
            'from_user_name' => $from_user_name,
        ];
    }
}
