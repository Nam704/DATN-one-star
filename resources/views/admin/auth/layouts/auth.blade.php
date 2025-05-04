<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title') | OneStar - Đăng nhập</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully responsive admin theme which can be used to build CRM, CMS,ERP etc." name="description" />
    <meta content="Techzaa" name="author" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin/assets/images/favicon.ico') }}">
    <!-- Theme Config Js -->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
    <!-- App css -->
    <link href="{{ asset('admin/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />
    <!-- Icons css -->
    <link href="{{ asset('admin/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
</head>

<body class="authentication-bg">
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5 position-relative">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-8 col-lg-10">
                    <div class="card overflow-hidden">
                        <div class="row g-0">
                            <div class="col-lg-6 d-none d-lg-block p-2">
                                <img src="/admin/assets/images/iphone-16-1-1700728104974843847493-1700785787200-17007857873541994545429.jpg" alt="img"
                                    class="img-fluid rounded h-100">
                            </div>
                            <div class="col-lg-6">
                                <div class="d-flex flex-column h-100">
                                    <div class="auth-brand p-4">
                                        <a href="#" class="logo-light">
                                            <img src="/client/assets/img/logo/logo-2.png" alt="img">
                                        </a>
                                        <a href="#" class="logo-dark">
                                            <img src="/client/assets/img/logo/logo-2.png" alt="img" width="200px">
                                        </a>
                                    </div>
                                    <div class="p-4 my-auto">
                                        @yield('content')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @yield('bottom-content')
        </div>
    </div>

    <footer class="footer footer-alt fw-medium">
        <span class="text-dark-emphasis">
            <script>
                document.write(new Date().getFullYear())
            </script> © OneStar - The best choices
        </span>
    </footer>

    <!-- Vendor js -->
    <script src="{{ asset('admin/assets/js/vendor.min.js') }}"></script>
    <!-- App js -->
    <script src="{{ asset('admin/assets/js/app.min.js') }}"></script>
    @yield('scripts')
</body>

</html>
