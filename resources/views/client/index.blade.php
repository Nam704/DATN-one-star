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
                        <h2><span> Sản phẩm của chúng tôi</span></h2>
                        <ul class="product_tab_button nav" role="tablist" id="nav-tab">

                            @foreach ($categories as $index => $item)
                                @if ($item->id_parent == 0)
                                    <li>
                                        <a class="{{ $index == 0 ? 'active' : '' }}" data-toggle="tab"
                                            href="#{{ $item->name }}" role="tab" aria-controls="{{ $item->name }}"
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
                @foreach ($categories as $index => $item)
                    @if ($item->id_parent == 0)
                        @php
                            $class = $index == 0 ? 'show active' : '';
                        @endphp
                        <div class="tab-pane fade {{ $class }}" id="{{ $item->name }}" role="tabpanel">
                            <div class="product_carousel product_column5 owl-carousel">
                                @foreach ($item->products as $product)
                                    <div class="single_product">
                                        <div class="product_name">
                                            <h3><a
                                                    href="{{ route('client.products.detail', $product->id) }}">{{ $product->name }}</a>
                                            </h3>
                                        </div>
                                        <div class="product_thumb">
                                            <a class="primary_img"
                                                href="{{ route('client.products.detail', $product->id) }}"><img
                                                    src="{{ asset($product->image_primary) }}" alt=""></a>

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
                                                    <span class="regular_price">
                                                        {{ number_format($product->min_price, 0, ',', '.') }} ₫</span>

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

            <!-- Phần Recommended Products -->
            <div class="row mt-3">
                <div class="col-12">
                    <div class="section_title">
                        <h2><span>Sản phẩm được đề xuất</span></h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="product_carousel product_column5 owl-carousel">
                        @forelse ($recommendedProducts as $product)
                            @php
                                // Sắp xếp các variant theo giá tăng dần và lấy variant đầu tiên (có giá thấp nhất)
                                $minVariant = $product->variants->sortBy('price')->first();
                            @endphp
                            <div class="single_product">
                                <div class="product_name">
                                    <h3>
                                        <a href="{{ route('client.products.detail', $product->id) }}">
                                            {{ $product->name }}
                                        </a>
                                    </h3>
                                </div>
                                <div class="product_thumb">
                                    <a class="primary_img" href="{{ route('client.products.detail', $product->id) }}">
                                        <img src="{{ asset($product->image_primary) }}" alt="">
                                    </a>
                                    <div class="label_product">
                                        @if ($product->discount_percent)
                                            <span class="label_sale">-{{ $product->discount_percent }}%</span>
                                        @endif
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
                                            @if ($minVariant)
                                                <span class="regular_price">
                                                    {{ number_format($minVariant->price, 0, ',', '.') }} ₫
                                                </span>
                                            @else
                                                <span class="regular_price">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>Không tìm thấy sản phẩm nào được đề xuất</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('scripts')

@endsection
