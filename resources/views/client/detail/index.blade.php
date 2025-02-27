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
                                    data-image="{{ asset($item->image_path)  }}"
                                    data-zoom-image="{{ asset($item->image_path)  }}">
                                    <img src="{{ asset($item->image_path)  }}" alt="zo-th-1" />
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
                                <li class="review"><a href="#"> (customer review ) </a></li>
                            </ul>

                        </div>
                        <div class="price_box">
                            <span class="current_price">{{ $product->min_price}} - {{ $product->max_price}}</span>
                            {{-- <span class="old_price">$80.00</span> --}}

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
                                    <h2>{{ $attribute["name"] }}</h2>
                                    <select class="select_option">
                                        @foreach ($attribute["values"] as $index=> $value)
                                        <option value="{{ $value }}">{{ $value }}</option>

                                        @endforeach


                                    </select>
                                    @endforeach
                                </div>

                            </div>
                            {{-- <h3>Available Options</h3>

                            @foreach ($product->attributes as $attribute)
                            <label>{{ $attribute["name"] }}</label>
                            <ul>

                                @foreach ($attribute["values"] as $index=> $value)
                                <li class="color{{ $index+1 }}"><a href="#"></a></li>
                                <li class=""><a href="#">{{ $value }}</a></li>

                                @endforeach
                            </ul>
                            @endforeach --}}
                        </div>
                        <div class="product_variant quantity">
                            <label>quantity</label>
                            <input min="1" max="100" value="1" type="number">
                            <button class="button" type="submit">add to cart</button>

                        </div>
                        <div class=" product_d_action">
                            <ul>
                                <li><a href="#" title="Add to wishlist">+ Add to Wishlist</a></li>
                                <li><a href="#" title="Add to wishlist">+ Compare</a></li>
                            </ul>
                        </div>
                        <div class="product_meta">
                            <span>Category: <a href="#">{{ $product->category->name }}</a></span>
                            <span>Brand: <a href="#">{{ $product->brand->name }}</a></span>

                        </div>

                    </form>
                    <div class="priduct_social">
                        <ul>
                            <li><a class="facebook" href="#" title="facebook"><i class="fa fa-facebook"></i> Like</a>
                            </li>
                            <li><a class="twitter" href="#" title="twitter"><i class="fa fa-twitter"></i> tweet</a></li>
                            <li><a class="pinterest" href="#" title="pinterest"><i class="fa fa-pinterest"></i> save</a>
                            </li>
                            <li><a class="google-plus" href="#" title="google +"><i class="fa fa-google-plus"></i>
                                    share</a></li>
                            <li><a class="linkedin" href="#" title="linkedin"><i class="fa fa-linkedin"></i> linked</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
{{-- @include('client.detail.product-info') --}}
@endsection