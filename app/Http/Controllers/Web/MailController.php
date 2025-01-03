<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    function sendMail()
    {
        $email = 'daovannam2k4@gmail.com';
        Mail::to($email)->send(new ContactMail);
    }
}
