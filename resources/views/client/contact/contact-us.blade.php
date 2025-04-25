@extends('client.layouts.home.layout')
@section('title', 'Liên hệ')

@section('content')
    <!--breadcrumbs area start-->
    <div class="breadcrumbs_area">
        <div class="container">
            <div class="row" style="margin-top: -20px">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <ul>
                            <li><a href="{{route('client.home')}}">Trang chủ</a></li>
                            <li>Liên hệ</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!--contact area start-->
    <div class="contact_area">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-12">
                    <div class="contact_message content">
                        <h3>Liên hệ với chúng tôi</h3>
                        <p>Chúng tôi ở đây để giúp đỡ và trả lời bất kỳ câu hỏi nào bạn có thể có. Hãy cho chúng tôi biết về
                            vấn đề của bạn để chúng tôi có thể giúp bạn nhanh hơn. Chúng tôi mong muốn được lắng nghe từ
                            bạn.</p>
                        <ul>
                            <li><i class="fa fa-fax"></i> Địa chỉ : FPT Polytechnic, đường Trịnh Văn Bô, Phương Canh, Nam Từ
                                Liêm, Hà Nội</li>
                            <li><i class="fa fa-envelope-o"></i> <a href="#">Onestar@gmail.com</a></li>
                            <li><i class="fa fa-phone"></i><a href="tel:0(1234)567890">0397183920</a> </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12">
                    <div class="contact_message form">
                        <h3>Gửi đến chúng tôi</h3>
                        @if (session('message'))
                            <div class="alert alert-primary" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session('message_error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('message_error') }}
                            </div>
                        @endif
                        <form action="{{ route('client.contact.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <p>
                                <label> Tên của bạn </label>
                                <input name="name" placeholder="Name *" type="text">
                                @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            </p>
                            <p>
                                <label> Email</label>
                                <input name="email" placeholder="Email *" type="email">
                                @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            </p>
                            <div class="contact_textarea">
                                <label> Nội dung</label>
                                <textarea placeholder="Message *" name="message" class="form-control2"></textarea>
                                @error('message')
                                    <p class="text-danger">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"> Gửi</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--contact area end-->


    <!--call to action start-->
    <section class="call_to_action">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="call_action_inner">
                        <div class="call_text">
                            <h3>We Have <span>Recommendations</span> for You</h3>
                            <p>Take 30% off when you spend $150 or more with code Autima11</p>
                        </div>
                        <div class="discover_now">
                            <a href="#">discover now</a>
                        </div>
                        <div class="link_follow">
                            <ul>
                                <li><a href="#"><i class="ion-social-facebook"></i></a></li>
                                <li><a href="#"><i class="ion-social-twitter"></i></a></li>
                                <li><a href="#"><i class="ion-social-googleplus"></i></a></li>
                                <li><a href="#"><i class="ion-social-youtube"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--call to action end-->
@endsection
