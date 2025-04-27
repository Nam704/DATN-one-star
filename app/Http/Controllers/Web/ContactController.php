<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;

class ContactController extends Controller
{
    public function index()
    {
        // Lọc các liên hệ có trạng thái 'pending'
    $contact = Contact::where('status', 'pending')->get();

    // Trả về view với dữ liệu đã lọc
    return view('admin.contact.index', compact('contact'));
    }

    public function trash()
{
    // Lấy tất cả các bản ghi đã bị xóa mềm
    $contacts = Contact::where('status', 'resolved')->get();
    
    // Trả về view và truyền dữ liệu
    return view('admin.contact.trash', compact('contacts'));
}


    public function show($id){
        $contact = Contact::find($id);
        return view('admin.contact.detail', compact('contact'));
    }

    public function edit($id){
        $contact = Contact::find($id);
        return view('admin.contact.edit', compact('contact'));
    }

    public function update(Request $request, $id){
        $request->validate([
            'reply' => 'required',
        ],
    [
        'reply.required'=> 'Không được để trống',
    ]);

        $contact = Contact::findOrFail($id);
        $contact->update([
            'reply' => $request->reply,
            'status' => 'resolved',
        ]);

        // Gửi email phản hồi
        Mail::to($contact->email)->send(new ContactReplyMail($contact));
  
        return redirect()->route('admin.contacts.trash')->with('success', 'Đã gửi phản hồi!');
    }
    public function delete($id)
    {
        // Tìm bản ghi trong bảng contact
        $contact = Contact::find($id);
    
        if ($contact) {
            $contact->delete();  // Xóa bản ghi trực tiếp khỏi cơ sở dữ liệu
            return redirect()->route('admin.contacts.trash')->with('success', 'Liên hệ đã bị xóa.');
        }
    
        return redirect()->route('admin.contacts.trash')->with('error', 'Liên hệ không tồn tại.');
    }
    

}
