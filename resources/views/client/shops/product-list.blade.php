@foreach ($products as $product)
    <div class="col-lg-4 col-md-4 col-12 pro"
        data-price="{{ $product->variants->isNotEmpty() ? $product->variants->first()->price : 0 }}">
        <div class="single_product">
            <div class="product_name grid_name">
                <h3><a href="product-details.html">{{ $product->name }}</a></h3>
                <p class="manufacture_product"><a href="#">Accessories</a></p>
            </div>
            <div class="product_thumb"
                style="width:200px; height:250px; overflow:hidden; position:relative; padding: 5px; margin: auto; background-color: #fff; box-sizing: border-box;">
                <a class="primary_img" href="product-details.html">
                    <img src="{{ asset($product->image_primary) }}" alt="{{ $product->name }}"
                        style="width:100%; height:100%; object-fit:contain; transition: opacity 0.3s; display:block;">
                </a>
                <a class="secondary_img" href="product-details.html" style="position:absolute; top:5px; left:5px;">
                    <img src="{{ asset($product->image_primary) }}" alt="{{ $product->name }}"
                        style="width:100%; height:100%; object-fit:contain; opacity:0; transition: opacity 0.3s; display:block;">
                </a>
                <div class="label_product">
                    <span class="label_sale">-47%</span>
                </div>
                <div class="action_links">
                    <ul>
                        <li class="quick_button">
                            <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick view">
                                <span class="lnr lnr-magnifier"></span>
                            </a>
                        </li>
                        <li class="wishlist">
                            <a href="wishlist.html" title="Add to Wishlist">git pull origin dev

                                <span class="lnr lnr-heart"></span>
                            </a>
                        </li>
                        <li class="compare">
                            <a href="compare.html" title="compare">
                                <span class="lnr lnr-sync"></span>
                            </a>
                        </li>
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
                            @php
                                // Lấy giá từ bảng product_variants (trường price)
                                $prices = $product->variants->pluck('price')->toArray();
                                $minPrice = !empty($prices) ? min($prices) : null;
                                $maxPrice = !empty($prices) ? max($prices) : null;
                            @endphp

                            @if ($minPrice !== null)
                                @if ($minPrice == $maxPrice)
                                    <span class="current_price">{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                                @else
                                    <span class="current_price">
                                        {{ number_format($minPrice, 0, ',', '.') }}đ -
                                        {{ number_format($maxPrice, 0, ',', '.') }}đ
                                    </span>
                                @endif
                            @else
                                <span class="current_price">Giá chưa cập nhật</span>
                            @endif
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
                        <h3><a href="product-details.html">{{ $product->name }}</a></h3>
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
                        @if ($minPrice !== null)
                            @if ($minPrice == $maxPrice)
                                <span class="current_price">{{ number_format($minPrice, 0, ',', '.') }}đ</span>
                            @else
                                <span class="current_price">
                                    {{ number_format($minPrice, 0, ',', '.') }}đ -
                                    {{ number_format($maxPrice, 0, ',', '.') }}đ
                                </span>
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
                            <li class="quick_button">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick view">
                                    <span class="lnr lnr-magnifier"></span>
                                </a>
                            </li>
                            <li class="wishlist">
                                <a href="wishlist.html" title="Add to Wishlist">
                                    <span class="lnr lnr-heart"></span>
                                </a>
                            </li>
                            <li class="compare">
                                <a href="compare.html" title="compare">
                                    <span class="lnr lnr-sync"></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
