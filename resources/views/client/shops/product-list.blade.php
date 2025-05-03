@foreach ($products as $product)
    <div class="col-lg-4 col-md-4 col-12 pro"
        data-price="{{ $product->variants->isNotEmpty() ? $product->variants->first()->price : 0 }}">
        <div class="single_product">
            <div class="product_name grid_name">
                <h3>
                    <a href="{{ route('client.products.detail', $product->id) }}">
                        {{ $product->name }}
                    </a>
                </h3>
            </div>
            <div class="product_thumb"
                style="width:200px; height:250px; overflow:hidden; position:relative; padding: 5px; margin: auto; background-color: #fff; box-sizing: border-box;">
                <a class="primary_img" href="{{ route('client.products.detail', $product->id) }}">
                    <img src="{{ asset($product->image_primary) }}" alt="{{ $product->name }}"
                        style="width:100%; height:100%; object-fit:contain; transition: opacity 0.3s; display:block;">
                </a>
                <a class="secondary_img" href="{{ route('client.products.detail', $product->id) }}"
                    style="position:absolute; top:5px; left:5px;">
                    <img src="{{ asset($product->image_primary) }}" alt="{{ $product->name }}"
                        style="width:100%; height:100%; object-fit:contain; opacity:0; transition: opacity 0.3s; display:block;">
                </a>
                {{-- <div class="action_links">
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
                </div> --}}
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
                            <span class="current_price">{{ number_format($product->min_price, 0, ',', '.') }}đ</span>
                        </div>
                        {{-- <div class="add_to_cart">
                            <a href="cart.html" title="add to cart">
                                <span class="lnr lnr-cart"></span>
                            </a>
                        </div> --}}
                    </div>
                </div>
            </div>
            <div class="product_content list_content">
                <div class="left_caption">
                    <div class="product_name">
                        <h3>
                            <a href="{{ route('client.products.detail', $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h3>
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
                        <span class="current_price">{{ number_format($product->min_price, 0, ',', '.') }}đ</span>
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
