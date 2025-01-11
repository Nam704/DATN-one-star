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

    public function __construct($importData)
    {
        $this->importData = $importData;
    }

    public function broadcastOn()
    {

        return new PrivateChannel('imports');
    }

    public function broadcastWith()
    {

        return [
            // 'importData' => $this->importData,
            'message' => 'Import completed successfully',
            // "user" => $this->user
        ];
    }
}
