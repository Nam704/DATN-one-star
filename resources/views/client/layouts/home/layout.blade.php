<!doctype html>
<html class="no-js" lang="en">


<!-- Mirrored from htmldemo.net/autima/autima/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 18 Dec 2024 14:55:36 GMT -->

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Autima - Car Accessories Shop HTML Template </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

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

    @include('client.layouts.home.left-sidebar')
    <!--slider area end-->

    <!--shipping area start-->
    <section class="shipping_area mb-50">
        <div class="container">
            <div class=" row">
                <div class="col-12">
                    <div class="shipping_inner">
                        <div class="single_shipping">
                            <div class="shipping_icone">
                                <img src="assets/img/about/shipping1.png" alt="">
                            </div>
                            <div class="shipping_content">
                                <h2>Free Shipping</h2>
                                <p>Free shipping on all US order</p>
                            </div>
                        </div>
                        <div class="single_shipping">
                            <div class="shipping_icone">
                                <img src="assets/img/about/shipping2.png" alt="">
                            </div>
                            <div class="shipping_content">
                                <h2>Support 24/7</h2>
                                <p>Contact us 24 hours a day</p>
                            </div>
                        </div>
                        <div class="single_shipping">
                            <div class="shipping_icone">
                                <img src="assets/img/about/shipping3.png" alt="">
                            </div>
                            <div class="shipping_content">
                                <h2>100% Money Back</h2>
                                <p>You have 30 days to Return</p>
                            </div>
                        </div>
                        <div class="single_shipping">
                            <div class="shipping_icone">
                                <img src="assets/img/about/shipping4.png" alt="">
                            </div>
                            <div class="shipping_content">
                                <h2>Payment Secure</h2>
                                <p>We ensure secure payment</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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