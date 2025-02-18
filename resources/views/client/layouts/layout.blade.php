<!doctype html>
<html class="no-js" lang="en">


<!-- Mirrored from htmldemo.net/autima/autima/shop.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 18 Dec 2024 14:56:29 GMT -->
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Autima - shop</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

     <!-- CSS
    ========================= -->

     <!--bootstrap min css-->
    @include('client.layouts.css')

</head>

<body>

    <!--header area start-->
    @include('client.layouts.header')
    <!--header area end-->

    <!--shop  area start-->
    <div class="shop_area shop_reverse">
        <div class="container">
            @yield('content')
        </div>
    </div>
    <!--shop  area end-->

    <!--footer area start-->
    @include('client.layouts.footer')
    <!--footer area end-->



 <!-- JS
============================================ -->
<!--jquery min js-->

</body>

</html>

@include('client.layouts.js')
