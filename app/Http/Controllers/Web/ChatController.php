<?php

namespace App\Http\Controllers\Web;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    function index()
    {
        return view('admin.chat.index');
    }
    public function sendMessage(Request $request)
    {
        // Giả sử bạn có thông tin người dùng trong session
        $user = auth()->user();

        // Lấy nội dung tin nhắn từ request
        $message = $request->input('message');

        // Phát sự kiện MessageSent
        broadcast(new MessageSent($message, $user));

        return response()->json(['status' => 'Message Sent']);
    }
}
