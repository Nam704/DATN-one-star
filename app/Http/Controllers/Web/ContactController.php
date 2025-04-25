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
        $contact = Contact::all();
        return view('admin.contact.index', compact('contact'));
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
        ]);

        $contact = Contact::findOrFail($id);
        $contact->update([
            'reply' => $request->reply,
            'status' => 'resolved',
        ]);

        // Gửi email phản hồi
        Mail::to($contact->email)->send(new ContactReplyMail($contact));
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('success', 'Đã gửi phản hồi!');
    }
}
