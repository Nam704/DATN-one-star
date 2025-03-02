@extends('client.layouts.home.layout')
@section('title', 'Trang chủ')
@section('left-sidebar')
@include('client.layouts.home.left-sidebar')
@endsection
@section('shipping-area')
@include('client.layouts.home.shipping-area')
@endsection
@section('content')
<section class="product_area mb-50">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section_title">
                    <h2><span> <strong>Our</strong>Products</span></h2>
                    <ul class="product_tab_button nav" role="tablist" id="nav-tab">

                        @foreach ($categories as $index=> $item)
                        @if ($item->id_parent == 0)
                        <li>
                            <a class="{{ $index == 0 ? 'active' : '' }}" data-toggle="tab" href="#{{ $item->name }}"
                                role="tab" aria-controls="{{ $item->name }}"
                                aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                {{ $item->name }}
                            </a>
                        </li>
                        @endif
                        @endforeach


                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content">
            @foreach ($categories as $index=> $item)
            @if ($item->id_parent == 0)
            @php
            $class = ($index == 0) ? "show active" : "";
            @endphp
            <div class="tab-pane fade {{ $class }}" id="{{ $item->name }}" role="tabpanel">
                <div class="product_carousel product_column5 owl-carousel">
                    @foreach ($item->products as $product)
                    <div class="single_product">
                        <div class="product_name">
                            <h3><a href="{{ route('client.products.detail',$product->id) }}">{{ $product->name }}</a>
                            </h3>
                            <p class="manufacture_product"><a href="#">Accessories</a></p>
                        </div>
                        <div class="product_thumb">
                            <a class="primary_img" href="{{ route('client.products.detail',$product->id) }}"><img
                                    src="{{ asset($product->image_primary) }}" alt=""></a>

                            <div class="label_product">
                                <span class="label_sale">-57%</span>
                            </div>

                            <div class="action_links">
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
                        <div class="product_content">
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
                                    <span class="regular_price"> {{ $product->min_price }}</span>

                                </div>
                                {{-- <div class="add_to_cart">
                                    <a href="cart.html" title="add to cart"><span class="lnr lnr-cart"></span></a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
            @endif
            @endforeach


        </div>
    </div>
</section>
@endsection