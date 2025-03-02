<!doctype html>
<html class="no-js" lang="en">


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




    <!-- JS
============================================ -->
    @include('client.layouts.home.js')



</body>


<!-- Mirrored from htmldemo.net/autima/autima/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 18 Dec 2024 14:56:09 GMT -->

</html>