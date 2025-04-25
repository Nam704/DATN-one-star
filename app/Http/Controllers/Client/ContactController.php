<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;

class ContactController extends Controller
{
    public function index()
    {
        return view('client.contact.contact-us');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:2|max:100',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'regex:/^[\w\.-]+@(fpt\.edu\.vn|gmail\.com)$/',
            ],
           'message' => 'required|string|min:10|max:1000',
        ],[
            'name.required' => 'Vui lòng nhập họ tên.',
            'name.string' => 'Họ tên không hợp lệ.',
            'name.min' => 'Họ tên phải có ít nhất :min ký tự.',
            'name.max' => 'Họ tên không được vượt quá :max ký tự.',

            'email.required' => 'Email không được trống',
            'email.regex' => 'Email không hợp lệ',
            'email.max' => 'Email không quá 255 ký tự',

            'message.required' => 'Vui lòng nhập nội dung liên hệ.',
            'message.string' => 'Nội dung không hợp lệ.',
            'message.min' => 'Nội dung phải từ :min ký tự trở lên.',
            'message.max' => 'Nội dung không được vượt quá :max ký tự.',
        ]);

        Contact::create($request->all());

        return redirect()->back()->with('message', 'Gửi liên hệ thành công!');
    }
}
