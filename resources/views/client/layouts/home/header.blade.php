<header class="header_area">
    <!--header top start-->
    <div class="header_top">
        <div class="container">
            <div class="top_inner">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="follow_us">

                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="top_right text-end">
                            <ul>
                                <i class="ion-android-person me-1"></i>
                                @if (Auth::check())
                                    <li class="top_links"><a href="#">
                                            {{ auth()->user()->name }}

                                            <i class="ion-ios-arrow-down"></i></a>
                                        <ul class="dropdown_links">
                                            <li><a href="{{ route('client.checkout.index') }}">Thanh toán </a></li>
                                            <li><a href="{{ route('client.user.myAccount') }}">Tải khoản của tôi </a></li>
                                            <li><a href="{{ route('client.carts.viewCart') }}">Giỏ hàng</a></li>

                                            <li class="nav-item">
                                                <a href="#" class="nav-link"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    Đăng xuất
                                                </a>
                                            </li>

                                            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST"
                                                style="display: none;">
                                                @csrf
                                            </form>

                                        </ul>
                                    </li>
                                @else
                                    <li class="top_links"><a href="#">
                                            Tài khoản
                                            <i class="ion-ios-arrow-down"></i></a>
                                        <ul class="dropdown_links">

                                            <li><a href="{{ route('auth.getFormLogin') }}">Đăng nhập</a></li>
                                            <li><a href="{{ route('auth.getFormRegister') }}">Đăng ký</a></li>
                                        </ul>
                                    </li>
                                @endif

                                <li class="language"><a href="#"><img
                                            src=" {{ asset('client/assets/img/logo/language.png') }}"
                                            alt="">english<i class="ion-ios-arrow-down"></i></a>
                                    <ul class="dropdown_language">
                                        <li><a href="#"><img
                                                    src=" {{ asset('client/assets/img/logo/language.png') }}"
                                                    alt=""> English</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="currency"><a href="#">VNĐ<i class="ion-ios-arrow-down"></i></a>

                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--header top start-->

    <!--header middel start-->
    <div class="header_middle">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3 col-md-6">
                    <div class="logo">
                        <a href="{{ route('client.home') }}"><img src="/client/assets/img/logo/logo-2.png"
                                alt="img"></a>
                    </div>
                </div>
                <div class="col-lg-9 col-md-6">
                    <div class="middel_right">
                        <div class="search-container mobile-search" style="position: relative;">
                            <form action="#">
                                <div class="search_box">
                                    <input type="text" class="search-input"
                                        placeholder="Search entire store here ..." autocomplete="off">
                                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                                </div>
                            </form>
                            <div class="search-result"
                                style="position: absolute; top: 100%; left: 0; width: 100%; z-index: 1000;"></div>
                        </div>

                        <div class="middel_right_info">

                            <div class="mini_cart_wrapper">
                                <a href="javascript:void(0)"><span class="lnr lnr-cart"></span>Giỏ hàng </a>
                                <span class="cart_quantity"></span>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--header middel end-->

    <!--mini cart-->
    @include('client.layouts.home.mini-cart')
    <!--mini cart end-->

    <!--header bottom satrt-->
    <div class="header_bottom sticky-header">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-12">
                    <div class="main_menu header_position">
                        <nav>
                            <ul>
                                <li><a href="{{route('client.home')}}">Trang chủ</a></li>
                                <li><a href="{{ route('client.shop') }}">Sản phẩm</a></li>
                                <li><a href="{{ route('client.blog.index') }}">Tin tức</a></li>
                                <li><a href="{{ route('client.contact.index') }}">Liên hệ với chúng tôi</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!--header bottom end-->
</header>
<!--Offcanvas menu area start-->
<div class="off_canvars_overlay"></div>
<div class="Offcanvas_menu">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="canvas_open">
                    <span>MENU</span>
                    <a href="javascript:void(0)"><i class="ion-navicon"></i></a>
                </div>
                <div class="Offcanvas_menu_wrapper">

                    <div class="canvas_close">
                        <a href="#"><i class="ion-android-close"></i></a>
                    </div>

                    <div class="top_right text-end">
                        <ul>
                            <li class="top_links"><a href="#"><i class="ion-android-person"></i> Tài khoản của tôi<i
                                        class="ion-ios-arrow-down"></i></a>
                                <ul class="dropdown_links">
                                    <li><a href="checkout.html">Thanh toán </a></li>
                                    <li><a href="{{ route('client.user.myAccount') }}">Tài khoản của tôi </a></li>
                                    <li><a href="cart.html">Giỏ hàng</a></li>
                                    <li><a href="wishlist.html">Danh sách mong muốn</a></li>
                                </ul>
                            </li>
                            <li class="language"><a href="#"><img
                                        src=" {{ asset('client/assets/img/logo/language.png') }}"
                                        alt="">en-gb<i class="ion-ios-arrow-down"></i></a>
                                <ul class="dropdown_language">
                                    <li><a href="#"><img
                                                src=" {{ asset('client/assets/img/logo/language.png') }}"
                                                alt=""> English</a></li>
                                    <li><a href="#"><img
                                                src=" {{ asset('client/assets/img/logo/language2.png') }}"
                                                alt=""> Germany</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="currency"><a href="#">$ USD<i class="ion-ios-arrow-down"></i></a>
                                <ul class="dropdown_currency">
                                    <li><a href="#">EUR – Euro</a></li>
                                    <li><a href="#">GBP – British Pound</a></li>
                                    <li><a href="#">INR – India Rupee</a></li>
                                </ul>
                            </li>

                        </ul>
                    </div>
                    <div class="Offcanvas_follow">
                        <label>Follow Us:</label>
                        <ul class="follow_link">
                            <li><a href="#"><i class="ion-social-facebook"></i></a></li>
                            <li><a href="#"><i class="ion-social-twitter"></i></a></li>
                            <li><a href="#"><i class="ion-social-googleplus"></i></a></li>
                            <li><a href="#"><i class="ion-social-youtube"></i></a></li>
                        </ul>
                    </div>
                    <div class="search-container mobile-search" style="position: relative;">
                        <form action="#">
                            <div class="search_box">
                                <input type="text" class="search-input" placeholder="Search entire store here ..."
                                    autocomplete="off">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </div>
                        </form>
                        <div class="search-result"
                            style="position: absolute; top: 100%; left: 0; width: 100%; z-index: 1000;"></div>
                    </div>
                    <div id="menu" class="text-left ">
                        <ul class="offcanvas_main_menu">
                            <li class="menu-item-has-children">
                                <a href="#">Home</a>
                                <ul class="sub-menu">
                                    <li><a href="index.html">Home 1</a></li>

                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!--Offcanvas menu area end-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.search-input').on('keyup', function() {
            var query = $(this).val();
            // Tìm container chứa ô tìm kiếm hiện hành và phần kết quả tương ứng
            var searchResultContainer = $(this).closest('.search-container').find('.search-result');
            if (query != '') {
                $.ajax({
                    url: "{{ route('client.search') }}",
                    type: "GET",
                    data: {
                        query: query
                    },
                    success: function(data) {
                        searchResultContainer.fadeIn();
                        searchResultContainer.html(data);
                    }
                });
            } else {
                searchResultContainer.fadeOut();
                searchResultContainer.html("");
            }
        });

        // Ẩn kết quả gợi ý khi click bên ngoài container search
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-container').length) {
                $('.search-result').fadeOut();
            }
        });
    });
</script>
