@extends('admin.mail.layouts.email')

@section('title', 'New Contact Message')

@section('content')
<div class="message-box">
    <h3>Message Details:</h3>
    <p><strong>From:</strong> {{ $data->name }}</p>
    <p><strong>Email:</strong> {{ $data->email }}</p>
    <p><strong>Subject:</strong> {{ $data->subject }}</p>

    <div class="message-content">
        <p><strong>Message:</strong></p>
        <p>{{ $data->message }}</p>
    </div>

    <div class="timestamp">
        <p><strong>Sent Date:</strong> {{ date('Y-m-d H:i:s') }}</p>
    </div>
</div>

<a href="{{ route('admin.contacts.index') }}" class="button">View All Messages</a>
@endsection