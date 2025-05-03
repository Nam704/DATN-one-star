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
        <div class="map mb-5">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3723.868088396546!2d105.74435187508114!3d21.0379634806137!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313455305afd834b%3A0x17268e09af37081e!2sT%C3%B2a%20nh%C3%A0%20FPT%20Polytechnic.!5e0!3m2!1svi!2s!4v1701953681942!5m2!1svi!2s" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
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
                            <li><i class="fa fa-phone"></i><a href="tel:0(1234)567890">0888888888</a> </li>
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
                                <input name="name" placeholder="Tên*" type="text" value="{{old('name')}}">
                                @error('name')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            </p>
                            <p>
                                <label> Email</label>
                                <input name="email" placeholder="Email *" type="email" value="{{old('email')}}">
                                @error('email')
                                <p class="text-danger">{{ $message }}</p>
                            @enderror
                            </p>
                            <div class="contact_textarea">
                                <label> Nội dung</label>
                                <textarea placeholder="Nội dung *" name="message" class="form-control2">{{old('message')}}</textarea>
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
@endsection
