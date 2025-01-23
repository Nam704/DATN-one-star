<?php

namespace App\Listeners;

use App\Events\ResetPassword;
use App\Mail\ForgotPasswordMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ResetPasswordNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        Log::info("In ResetPasswordNotification");
    }

    /**
     * Handle the event.
     */
    public function handle(ResetPassword $event): void
    {

        $user = $event->data["user"];
        $password_reset_token_plaintext = $event->data["token"];
        Log::info("In ResetPasswordNotification: " . $user->email . " " . $password_reset_token_plaintext);
        Mail::to($user->email)->send(new ForgotPasswordMail($user, $password_reset_token_plaintext));


        //
    }
    public function failed(ResetPassword $event, Throwable $exception)
    {
        Log::info("In ResetPasswordNotification: ", $event);
        Log::error("In ResetPassword: " . $exception);
    }
}
