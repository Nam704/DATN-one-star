{{-- @extends('admin.layouts.layout')
@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Chat-->
            <div class="card">
                <div class="card-body p-0">
                    <div class="p-3">
                        <div class="card-widgets">
                            <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                            <a data-bs-toggle="collapse" href="#yearly-sales-collapse" role="button" aria-expanded="false"
                                aria-controls="yearly-sales-collapse"><i class="ri-subtract-line"></i></a>
                            <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                        </div>
                        <h5 class="header-title mb-0">Chat</h5>
                    </div>

                    <div id="yearly-sales-collapse" class="collapse show">
                        <div class="chat-conversation mt-2">
                            <div class="card-body py-0 mb-3" data-simplebar style="height: 322px;">
                                <ul class="conversation-list">
                                    <li class="clearfix">
                                        <div class="chat-avatar">
                                            <img src="{{ asset('admin/assets/images/users/avatar-5.jpg') }}" alt="male">
                                            <i>10:00</i>
                                        </div>
                                        <div class="conversation-text">
                                            <div class="ctext-wrap">
                                                <i>Geneva</i>
                                                <p>
                                                    Hello!
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="clearfix odd">
                                        <div class="chat-avatar">
                                            <img src="{{ asset('admin/assets/images/users/avatar-1.jpg') }}" alt="Female">
                                            <i>10:01</i>
                                        </div>
                                        <div class="conversation-text">
                                            <div class="ctext-wrap">
                                                <i>Thomson</i>
                                                <p>
                                                    Hi, How are you? What about our next meeting?
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="clearfix">
                                        <div class="chat-avatar">
                                            <img src="{{ asset('admin/assets/images/users/avatar-5.jpg') }}" alt="male">
                                            <i>10:01</i>
                                        </div>
                                        <div class="conversation-text">
                                            <div class="ctext-wrap">
                                                <i>Geneva</i>
                                                <p>
                                                    Yeah everything is fine
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="clearfix odd">
                                        <div class="chat-avatar">
                                            <img src="{{ asset('admin/assets/images/users/avatar-1.jpg') }}" alt="male">
                                            <i>10:02</i>
                                        </div>
                                        <div class="conversation-text">
                                            <div class="ctext-wrap">
                                                <i>Thomson</i>
                                                <p>
                                                    Wow that's great
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="card-body pt-0">
                                <form class="needs-validation" novalidate name="chat-form" id="chat-form">
                                    <div class="row align-items-start">
                                        <div class="col">
                                            <input type="text" class="form-control chat-input"
                                                placeholder="Enter your text" required>
                                            <div class="invalid-feedback">
                                                Please enter your messsage
                                            </div>
                                        </div>
                                        <div class="col-auto d-grid">
                                            <button type="submit"
                                                class="btn btn-danger chat-send waves-effect waves-light">Send</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div> <!-- end .chat-conversation-->
                    </div>
                </div>

            </div> <!-- end card-->
        </div>
    </div>
@endsection --}}
@extends('admin.layouts.layout')

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Chat-->
            <div class="card">
                <div class="card-body p-0">
                    <div class="p-3">
                        <div class="card-widgets">
                            <a href="javascript:;" data-bs-toggle="reload"><i class="ri-refresh-line"></i></a>
                            <a data-bs-toggle="collapse" href="#yearly-sales-collapse" role="button" aria-expanded="false"
                                aria-controls="yearly-sales-collapse"><i class="ri-subtract-line"></i></a>
                            <a href="#" data-bs-toggle="remove"><i class="ri-close-line"></i></a>
                        </div>
                        <h5 class="header-title mb-0">Chat</h5>
                    </div>

                    <div id="yearly-sales-collapse" class="collapse show">
                        <div class="chat-conversation mt-2">
                            <div class="card-body py-0 mb-3" data-simplebar style="height: 322px;">
                                <ul class="conversation-list" id="message-box">
                                    <!-- Tin nhắn sẽ được thêm ở đây -->
                                </ul>
                            </div>
                            <div class="card-body pt-0">
                                <form class="needs-validation" novalidate name="chat-form" id="chat-form">
                                    <div class="row align-items-start">
                                        <div class="col">
                                            <input type="text" class="form-control chat-input" id="message-input"
                                                placeholder="Enter your text" required>
                                            <div class="invalid-feedback">
                                                Please enter your message
                                            </div>
                                        </div>
                                        <div class="col-auto d-grid">
                                            <button type="submit" class="btn btn-danger chat-send waves-effect waves-light"
                                                id="send-message-btn">Send</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                        </div> <!-- end .chat-conversation-->
                    </div>
                </div>
            </div> <!-- end card-->
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/chat.js')
@endpush
