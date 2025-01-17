<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImportNotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $importData;
    public $user;
    public function __construct($importData, $user)
    {
        $this->importData = $importData;
        $this->user = $user;
    }

    public function broadcastOn()
    {

        return new PrivateChannel('imports');
    }

    public function broadcastWith()
    {

        return [
            'title' => 'Import Notification',
            'message' => $this->user->name . " đã thêm sản phẩm mới vào hệ thống",
            'id' => $this->importData->id,
        ];
    }
}
