@extends('client.layouts.app')
@section('content')
<div class="off_canvars_overlay"></div>
<div class="shop_area shop_fullwidth">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="shop_title">
                    <h1>Kết quả tìm kiếm cho: "{{ $keyword }}"</h1>
                </div>

                @if($products->count() > 0)
                    <div class="row shop_wrapper">
                        @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-12">
                            <div class="single_product">
                                <div class="product_name grid_name">
                                    <h3><a href="product-details.html">{{ $product->name }}</a></h3>
                                    <p class="manufacture_product"><a href="#">{{ $product->category->name ?? 'Uncategorized' }}</a></p>
                                </div>
                                <div class="product_thumb">
                                    <a class="primary_img" href="product-details.html">
                                        <img src="{{ Storage::url($product->image_primary) }}" alt="{{ $product->name }}">
                                    </a>
                                    @if($product->variants->first() && $product->variants->first()->price_sale > 0)
                                        <div class="label_product">
                                            <span class="label_sale">-{{ $product->variants->first()->discount_percent }}%</span>
                                        </div>
                                    @endif
                                    <div class="action_links">
                                        <ul>
                                            <li class="quick_button"><a href="#" data-bs-toggle="modal" data-bs-target="#modal_box" title="quick view"><span class="lnr lnr-magnifier"></span></a></li>
                                            <li class="wishlist"><a href="wishlist.html" title="Add to Wishlist"><span class="lnr lnr-heart"></span></a></li>
                                            <li class="compare"><a href="compare.html" title="compare"><span class="lnr lnr-sync"></span></a></li>
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
                                                @if($product->variants->first())
                                                    <span class="current_price">{{ number_format($product->variants->first()->price_sale) }}đ</span>
                                                    <span class="old_price">{{ number_format($product->variants->first()->price) }}đ</span>
                                                @endif
                                            </div>
                                            <div class="add_to_cart">
                                                <a href="cart.html" title="add to cart"><span class="lnr lnr-cart"></span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="shop_toolbar t_bottom">
                        <div class="pagination">
                            <ul>
                                @if ($products->onFirstPage())
                                    <li class="current">1</li>
                                @else
                                    <li><a href="{{ $products->previousPageUrl() }}">Previous</a></li>
                                @endif
                    
                                @for ($i = 1; $i <= $products->lastPage(); $i++)
                                    <li class="{{ ($products->currentPage() == $i) ? 'current' : '' }}">
                                        <a href="{{ $products->url($i) }}">{{ $i }}</a>
                                    </li>
                                @endfor
                    
                                @if ($products->hasMorePages())
                                    <li class="next"><a href="{{ $products->nextPageUrl() }}">next</a></li>
                                    <li><a href="{{ $products->url($products->lastPage()) }}">>></a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    
                @else
                    <div class="alert alert-info text-center">
                        Không tìm thấy sản phẩm nào phù hợp với từ khóa "{{ $keyword }}"
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
