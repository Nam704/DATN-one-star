<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        return $this->subject('Password Reset Request')
            ->view('admin.mail.forgot-password')
            ->with([
                'password_reset_token' => $this->token,
                'resetLink' => route('auth.getfromResetPassword', ['token' => $this->token]),
            ]);
    }
}
