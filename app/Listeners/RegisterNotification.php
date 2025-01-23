<?php

namespace App\Listeners;

use App\Events\Register;
use App\Mail\RegisterMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class RegisterNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public $user;
    public function __construct()
    { }

    /**
     * Handle the event.
     */
    public function handle(Register $event): void
    {
        $this->user = $event->data;
        Log::info("message sent: " . $this->user->email);

        Mail::to($this->user->email)->send(new RegisterMail($this->user));
    }
    public function failed(Register $event, Throwable $exception)
    {
        Log::error("message not sent: " . $event->data);
        Log::error("In Register: " . $exception);
    }
}
