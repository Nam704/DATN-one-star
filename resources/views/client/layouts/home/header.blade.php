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
                                            <li><a href="{{ route('client.checkout.index') }}">Checkout </a></li>
                                            <li><a href="{{ route('client.user.myAccount') }}">My Account </a></li>
                                            <li><a href="{{ route('client.carts.viewCart') }}">Shopping Cart</a></li>

                                            <li class="nav-item">
                                                <a href="#" class="nav-link"
                                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                                    Logout
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
                                            My Account
                                            <i class="ion-ios-arrow-down"></i></a>
                                        <ul class="dropdown_links">

                                            <li><a href="{{ route('auth.getFormLogin') }}">Login</a></li>

                                        </ul>
                                    </li>
                                @endif

                                <li class="language"><a href="#"><img
                                            src=" {{ asset('client/assets/img/logo/language.png') }}"
                                            alt="">en-gb<i class="ion-ios-arrow-down"></i></a>
                                    <ul class="dropdown_language">
                                        <li><a href="#"><img
                                                    src=" {{ asset('client/assets/img/logo/language.png') }}"
                                                    alt=""> English</a>
                                        </li>

                                    </ul>
                                </li>
                                <li class="currency"><a href="#">$ VNĐ<i class="ion-ios-arrow-down"></i></a>

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
                            <form action="{{ route('client.shop') }}" method="GET" class="search-form">
                                <div class="search_box">
                                    <input type="text" name="search" class="search-input"
                                        placeholder="Search entire store here …" autocomplete="off">
                                    <button type="submit"><i class="ion-ios-search-strong"></i></button>
                                </div>
                            </form>
                            <div class="search-result"
                                style="position:absolute; top:100%; left:0; width:100%; z-index:1000;"></div>
                        </div>

                        <div class="middel_right_info">

                            <div class="mini_cart_wrapper">
                                <a href="javascript:void(0)"><span class="lnr lnr-cart"></span>My Cart </a>
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
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="main_menu header_position text-center">
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
                            <li class="top_links"><a href="#"><i class="ion-android-person"></i> My Account<i
                                        class="ion-ios-arrow-down"></i></a>
                                <ul class="dropdown_links">
                                    <li><a href="checkout.html">Checkout </a></li>
                                    <li><a href="{{ route('client.user.myAccount') }}">My Account </a></li>
                                    <li><a href="cart.html">Shopping Cart</a></li>
                                    <li><a href="wishlist.html">Wishlist</a></li>
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
                        <form action="{{ route('client.shop') }}" method="GET" class="search-form">
                            <div class="search_box">
                                <input type="text" name="search" class="search-input"
                                    placeholder="Search entire store here …" autocomplete="off">
                                <button type="submit"><i class="ion-ios-search-strong"></i></button>
                            </div>
                        </form>
                        <div class="search-result"
                            style="position:absolute; top:100%; left:0; width:100%; z-index:1000;"></div>
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
$(function(){
  // URL cho AJAX dropdown
  var dropdownUrl = "{{ route('client.search') }}";

  // Gợi ý realtime khi gõ
  $('.search-input').on('keyup', function(){
    var q    = $(this).val().trim();
    var $res = $(this).closest('.search-container').find('.search-result');

    if (q) {
      $.get(dropdownUrl, { query: q })
       .done(function(html){
         $res.fadeIn().html(html);
       });
    } else {
      $res.fadeOut().empty();
    }
  });

  // Ẩn dropdown khi click ngoài
  $(document).on('click', function(e){
    if (!$(e.target).closest('.search-container').length) {
      $('.search-result').fadeOut();
    }
  });

  // Click chọn item trong dropdown
  $(document).on('click', '.search-result .dropdown-item a', function(e){
    e.preventDefault();

    var $a       = $(this);
    var type     = $a.data('type');    // "brand" hoặc "category"
    var id       = $a.data('id');      // id của item
    var $wrap    = $a.closest('.search-container');
    var $input   = $wrap.find('.search-input');
    var $result  = $wrap.find('.search-result');

    // 1) Đánh dấu checkbox tương ứng
    if (type === 'brand') {
      $('input.brand-filter[value="' + id + '"]').prop('checked', true);
    } else if (type === 'category') {
      $('input.category-filter[value="' + id + '"]').prop('checked', true);
    }

    // 2) Xóa nội dung ô tìm kiếm
    $input.val('');

    // 3) Ẩn dropdown
    $result.fadeOut();

    // 4) Gọi lại filter nếu có
    if (typeof fetchFilteredProducts === 'function') {
      fetchFilteredProducts();
    }
  });
});
</script>
