@extends('client.layouts.home.layout')

@section('title', 'Chi tiết sản phẩm')

@section('content')
<div class="product_details mt-20">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="product-details-tab">

                    <div id="img-1" class="zoomWrapper single-zoom">
                        <a href="#">
                            <img id="zoom1" src="{{ asset($product->image_primary) }}"
                                data-zoom-image="{{ asset($product->image_primary) }}" alt="big-1">
                        </a>
                    </div>

                    <div class="single-zoom-thumb">
                        <ul class="s-tab-zoom owl-carousel single-product-active" id="gallery_01">
                            @foreach ($product->product_albums as $item)
                            <li>
                                <a href="#" class="elevatezoom-gallery active" data-update=""
                                    data-image="{{ asset($item->image_path) }}"
                                    data-zoom-image="{{ asset($item->image_path) }}">
                                    <img src="{{ asset($item->image_path) }}" alt="zo-th-1" />
                                </a>

                            </li>
                            @endforeach

                        </ul>
                    </div>

                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="product_d_right">
                    <form action="#">

                        <h1>{{ $product->name }}</h1>
                        {{-- <div class="product_nav">
                            <ul>
                                <li class="prev"><a href="product-details.html"><i class="fa fa-angle-left"></i></a>
                                </li>
                                <li class="next"><a href="variable-product.html"><i class="fa fa-angle-right"></i></a>
                                </li>
                            </ul>
                        </div> --}}
                        <div class=" product_ratting">
                            <ul>
                                <li><a href="#"><i class="fa fa-star"></i></a></li>
                                <li><a href="#"><i class="fa fa-star"></i></a></li>
                                <li><a href="#"><i class="fa fa-star"></i></a></li>
                                <li><a href="#"><i class="fa fa-star"></i></a></li>
                                <li><a href="#"><i class="fa fa-star"></i></a></li>
                                <li class="review"><a href="#"> (Đánh giá khách hàng ) </a></li>
                            </ul>

                        </div>
                        <div class="price_box">
                            <span class="current_price" id="price-box"></span>

                        </div>
                        {{-- <div class="product_desc">
                            <p>eget velit. Donec ac tempus ante. Fusce ultricies massa massa. Fusce aliquam, purus eget
                                sagittis vulputate, sapien libero hendrerit est, sed commodo augue nisi non neque. Lorem
                                ipsum dolor sit amet, consectetur adipiscing elit. Sed tempor, lorem et placerat
                                vestibulum, metus nisi posuere nisl, in </p>
                        </div> --}}
                        <div class="variants_selects ">
                            <div class="variants_size">
                                <div class="row">
                                    @foreach ($product->attributes as $attribute)
                                    @php
                                    $uniqueValues = [];
                                    @endphp
                                    <h2>{{ $attribute['name'] }}</h2>
                                    <select class="value-select form-control"
                                        name="attribute[{{ $attribute['name'] }}]">
                                        <option value="">Chọn</option>
                                        @foreach ($attribute['values'] as $variant_id => $values)
                                        @foreach ($values as $value_id => $value)
                                        @if (!in_array($value, $uniqueValues))
                                        <option value="{{ $value_id }}"
                                            data-variant-id={{ $variant_id }}>{{ $value }}
                                        </option>
                                        @endif

                                        @php
                                        $uniqueValues[] = $value;
                                        @endphp
                                        @endforeach
                                        @endforeach
                                    </select>
                                    @endforeach
                                </div>

                            </div>

                        </div>
                        <div class="product_variant quantity mt-3">
                            <label>Số lượng</label>
                            <input min="1" max="1" value="1" type="number" class="quantity-to-cart">
                            <label>Còn : <a href="#" class="stock">{{ $product->quantity }}</a></label>
                            <button class="button" id="add-to-cart">Thêm vào giỏ hàng</button>

                        </div>
                        <div class=" product_d_action">
                            <ul>
                                <li>Danh mục: {{ $product->category->name }}</li>
                                <li>Thương hiệu:{{ $product->brand->name }}</li>
                            </ul>
                        </div>


                    </form>
                    <div class="priduct_social">

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@include('client.detail.product-info', ['relatedProducts' => $relatedProducts, 'comments' => $comments])
<script>
    var product = @json($product);
</script>

@endsection
@section('scripts')
@vite('resources/js/client/productDetail.js')
@endsection