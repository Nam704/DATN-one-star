<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Autima - product details</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">



    <script src="{{ asset('client/assets/js/sweetalert2.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    

    <!-- CSS
    ========================= -->
    <!--bootstrap min css-->

    <link rel="stylesheet" href="{{ asset('client/assets/css/bootstrap.min.css') }}" />
    <!--owl carousel min css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/owl.carousel.min.css') }}" />
    <!--slick min css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/slick.css') }}" />
    <!--magnific popup min css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/magnific-popup.css') }}" />
    <!--font awesome css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/font.awesome.css') }}" />
    <!--ionicons min css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/ionicons.min.css') }}" />
    <!--animate css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/animate.css') }}" />
    <!--jquery ui min css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/jquery-ui.min.css') }}" />
    <!--slinky menu css-->
    <link rel="stylesheet" href="{{ asset('client/assets/css/slinky.menu.css') }}" />
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/css/plugins.css') }}" />
    <!-- Main Style CSS -->
    <link rel="stylesheet" href="{{ asset('client/assets/css/style.css') }}" />
    <!--modernizr min js here-->

</head>


<body>
    @include('client.layouts.header')
    <!-- Begin page -->
    <div class="wrapper">
        @yield('content')
    </div>
    <!-- END wrapper -->
    @include('client.layouts.footer')
</body>



</html>
