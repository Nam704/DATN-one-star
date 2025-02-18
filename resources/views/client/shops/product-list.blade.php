{{-- product-list.blade.php --}}
<div class="shop_banner">
    <img src="assets/img/bg/banner8.jpg" alt="">
</div>
<div class="shop_title">
    <h1>shop</h1>
</div>
<div class="shop_toolbar_wrapper">
    <div class="shop_toolbar_btn">

        <button data-role="grid_3" type="button" class="active btn-grid-3" data-toggle="tooltip" title="3"></button>

        <button data-role="grid_4" type="button" class=" btn-grid-4" data-toggle="tooltip" title="4"></button>

        <button data-role="grid_list" type="button" class="btn-list" data-toggle="tooltip" title="List"></button>
    </div>
    <div class="niceselect_option">
        <form class="select_option" action="#">
            <select name="orderby" id="short">
                <option selected value="1">Sort by average rating</option>
                <option value="2">Sort by popularity</option>
                <option value="3">Sort by newness</option>
                <option value="4">Sort by price: low to high</option>
                <option value="5">Sort by price: high to low</option>
                <option value="6">Product Name: A→Z</option>
                <option value="7">Product Name: Z→A</option>
            </select>
        </form>
    </div>

    <div class="page_amount">
        <p>Showing 1–9 of 21 results</p>
    </div>
</div>
@foreach ($products as $product)

    <div class="col-lg-4 col-md-4 col-12 ">
        <div class="single_product">
            <div class="product_name grid_name">
                <h3><a href="product-details.html">{{ $product->name }}</a></h3>
                <p class="manufacture_product"><a href="#">Accessories</a></p>
            </div>
            <div class="product_thumb">
                <a class="primary_img" href="product-details.html">
                    <img src="{{ asset('storage/' . $product->image_primary) }}" alt="{{ $product->name }}"
                        height="100px" width="300px">
                </a>
                <a class="secondary_img" href="product-details.html">
                    <img src="{{ asset('storage/' . $product->image_primary) }}" alt="{{ $product->name }}">
                </a>
                <div class="label_product">
                    <span class="label_sale">-47%</span>
                </div>
                <div class="action_links">
                    <ul>
                        <li class="quick_button"><a href="#" data-bs-toggle="modal" data-bs-target="#modal_box"
                                title="quick view"> <span class="lnr lnr-magnifier"></span></a></li>
                        <li class="wishlist"><a href="wishlist.html" title="Add to Wishlist"><span
                                    class="lnr lnr-heart"></span></a></li>
                        <li class="compare"><a href="compare.html" title="compare"><span
                                    class="lnr lnr-sync"></span></a></li>
                    </ul>
                </div>
            </div>
            <div class="product_content grid_content">
                <div class="content_inner">
                    <div class="product_ratings">
                        <ul>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                        </ul>
                    </div>
                    <div class="product_footer d-flex align-items-center">
                        <div class="price_box">
                            <span class="current_price">
                                @if ($product->variants->isNotEmpty() && $product->variants->first()->importDetails->isNotEmpty())
                                    <span class="current_price">
                                        {{ number_format($product->variants->first()->importDetails->first()->expected_price, 0, ',', '.') }}đ
                                    </span>
                                @else
                                    <span class="current_price">Giá chưa cập nhật</span>
                                @endif
                            </span>
                        </div>
                        <div class="add_to_cart">
                            <a href="cart.html" title="add to cart"><span class="lnr lnr-cart"></span></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product_content list_content">
                <div class="left_caption">
                    <div class="product_name">
                        <h3><a href="product-details.html">Cas Meque Metus Shoes Core i7 3.4GHz, 16GB DDR3</a></h3>
                    </div>
                    <div class="product_ratings">
                        <ul>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                            <li><a href="#"><i class="ion-star"></i></a></li>
                        </ul>
                    </div>

                    <div class="product_desc">
                        <p>{{ $product->description }}</p>
                    </div>
                </div>
                <div class="right_caption">
                    <div class="text_available">
                        <p>availabe: <span>99 in stock</span></p>
                    </div>
                    <div class="price_box">
                        @if ($product->variants->isNotEmpty())
                            @php
                                $prices = [];
                                // Duyệt qua từng biến thể và lấy giá từ importDetails
                                foreach ($product->variants as $variant) {
                                    if ($variant->importDetails->isNotEmpty()) {
                                        // Giả sử bạn lấy giá từ phần tử đầu tiên của importDetails
                                        $prices[] = $variant->importDetails->first()->expected_price;
                                    }
                                }
                                $minPrice = !empty($prices) ? min($prices) : null;
                                $maxPrice = !empty($prices) ? max($prices) : null;
                            @endphp

                            @if ($minPrice !== null)
                                @if ($minPrice == $maxPrice)
                                    <span class="current_price">{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="current_price">
                                        Từ {{ number_format($minPrice, 0, ',', '.') }}đ đến
                                        {{ number_format($maxPrice, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            @else
                                <span class="current_price">Giá chưa cập nhật</span>
                            @endif
                        @else
                            <span class="current_price">Giá chưa cập nhật</span>
                        @endif

                    </div>
                    <div class="cart_links_btn">
                        <a href="#" title="add to cart">add to cart</a>
                    </div>
                    <div class="action_links_btn">
                        <ul>
                            <li class="quick_button"><a href="#" data-bs-toggle="modal"
                                    data-bs-target="#modal_box" title="quick view"> <span
                                        class="lnr lnr-magnifier"></span></a></li>
                            <li class="wishlist"><a href="wishlist.html" title="Add to Wishlist"><span
                                        class="lnr lnr-heart"></span></a></li>
                            <li class="compare"><a href="compare.html" title="compare"><span
                                        class="lnr lnr-sync"></span></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
