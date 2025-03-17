<!doctype html>
<html class="no-js" lang="en">
<meta name="user-data" content='@json(Auth::user())'>
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Mirrored from htmldemo.net/autima/autima/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 18 Dec 2024 14:55:36 GMT -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'OneStar Shop')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    {{--
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico"> --}}

    <!-- CSS
    ========================= -->
    @include('client.layouts.css')

</head>

<body>

    <!-- Main Wrapper Start -->
    <!--header area start-->
    @include('client.layouts.home.header')
    <!--header area end-->

    <!--slider area start-->
    @yield('left-sidebar')
    {{-- @include('client.layouts.home.left-sidebar') --}}
    <!--slider area end-->

    <!--shipping area start-->
    @yield('shipping-area')
    {{-- @include('client.layouts.home.shipping-area') --}}
    <!--shipping area end-->

    <!--product area start-->
    {{-- @include('client.layouts.home.product') --}}
    @yield('content')
    <!--call to action end-->

    <!--footer area start-->
    @include('client.layouts.home.footer')

    <!--news letter popup start-->

    <script>
        var userMeta = document.querySelector('meta[name="user-data"]').getAttribute('content');
        var user = userMeta ? JSON.parse(userMeta) : null;

        var csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
    </script>

    <!-- JS
============================================ -->
    @include('client.layouts.home.js')
    @vite('resources/js/client.js')

</body>

<!-- Mirrored from htmldemo.net/autima/autima/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 18 Dec 2024 14:56:09 GMT -->

</html>
