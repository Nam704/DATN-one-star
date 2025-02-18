<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title') - One Star</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('client/assets/img/favicon.ico') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/font.awesome.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/ionicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/slinky.menu.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/plugins.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/css/style.css') }}">
</head>
<body>
    @include('client.layouts.header')
    
    @yield('content')

    @include('client.layouts.footer')

    <!-- Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="{{ asset('client/assets/js/vendor/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('client/assets/js/popper.js') }}"></script>
    <script src="{{ asset('client/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('client/assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('client/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('client/assets/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('client/assets/js/jquery.countdown.js') }}"></script>
    <script src="{{ asset('client/assets/js/jquery.ui.js') }}"></script>
    <script src="{{ asset('client/assets/js/jquery.elevatezoom.js') }}"></script>
    <script src="{{ asset('client/assets/js/isotope.pkgd.min.js') }}"></script>
    <script src="{{ asset('client/assets/js/slinky.menu.js') }}"></script>
    <script src="{{ asset('client/assets/js/plugins.js') }}"></script>
    <script src="{{ asset('client/assets/js/main.js') }}"></script>
    <script src="{{ asset('client/assets/js/cart.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</body>
</html>
