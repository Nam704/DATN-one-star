<div class="product_d_info">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="product_d_inner">
                    <div class="product_info_button">
                        <ul class="nav" role="tablist" id="nav-tab">
                            <li>
                                <a class="active" data-toggle="tab" href="#info" role="tab" aria-controls="info"
                                    aria-selected="false">Mô tả</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#sheet" role="tab" aria-controls="sheet"
                                    aria-selected="false">Thông số kỹ thuật</a>
                            </li>
                            <li>
                                <a data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews"
                                    aria-selected="false">Bình luận đánh giá</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="info" role="tabpanel">
                            <div class="product_info_content text-center">
                                {!! $product->description !!}
                            </div>
                        </div>

                        <div class="tab-pane fade" id="sheet" role="tabpanel">
                            <div class="product_d_table">
                                <table class="table table-bordered text-center align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="first_child">Thuộc tính</th>
                                            <th>Thông tin</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($product->attributes as $attribute)
                                        @php
                                        $allValues = [];
                                        foreach ($attribute['values'] as $variants) {
                                        foreach ($variants as $value) {
                                        if (!in_array($value, $allValues)) {
                                        $allValues[] = $value;
                                        }
                                        }
                                        }
                                        @endphp
                                        <tr>
                                            <td class="first_child">{{ $attribute['name'] }}</td>
                                            <td>{{ implode(', ', $allValues) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>



                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <div class="reviews_wrapper">
                                <h2>1 review for Donec eu furniture</h2>
                                <div class="reviews_comment_box">
                                    <div class="comment_thmb">
                                        <img src="assets/img/blog/comment2.jpg" alt="">
                                    </div>
                                    <div class="comment_text">
                                        <div class="reviews_meta">
                                            <div class="star_rating">
                                                <ul>
                                                    <li><a href="#"><i class="ion-ios-star"></i></a></li>
                                                    <li><a href="#"><i class="ion-ios-star"></i></a></li>
                                                    <li><a href="#"><i class="ion-ios-star"></i></a></li>
                                                    <li><a href="#"><i class="ion-ios-star"></i></a></li>
                                                    <li><a href="#"><i class="ion-ios-star"></i></a></li>
                                                </ul>
                                            </div>
                                            <p><strong>admin </strong>- September 12, 2018</p>
                                            <span>roadthemes</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="comment_title">
                                    <h2>Add a review </h2>
                                    <p>Your email address will not be published. Required fields are marked </p>
                                </div>
                                <div class="product_ratting mb-10">
                                    <h3>Your rating</h3>
                                    <ul>
                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                        <li><a href="#"><i class="fa fa-star"></i></a></li>
                                    </ul>
                                </div>
                                <div class="product_review_form">
                                    <form action="#">
                                        <div class="row">
                                            <div class="col-12">
                                                <label for="review_comment">Your review </label>
                                                <textarea name="comment" id="review_comment"></textarea>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <label for="author">Name</label>
                                                <input id="author" type="text">

                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <label for="email">Email </label>
                                                <input id="email" type="text">
                                            </div>
                                        </div>
                                        <button type="submit">Submit</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--product area start-->
<section class="product_area mb-50">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="section_title">
                    <h2><span><strong>Related</strong> Products</span></h2>
                </div>
                <div class="product_carousel product_column5 owl-carousel">
                    @if ($relatedProducts->count() > 0)
                    @foreach ($relatedProducts as $related)
                    <div class="single_product">
                        <div class="product_thumb">
                            <a class="primary_img" href="{{ route('client.products.detail', $related->id) }}">
                                <img src="{{ asset($related->image_primary) }}" alt="{{ $related->name }}">
                            </a>
                            @if ($related->image_secondary)
                            <a class="secondary_img"
                                href="{{ route('client.products.detail', $related->id) }}">
                                <img src="{{ asset($related->image_secondary) }}"
                                    alt="{{ $related->name }}">
                            </a>
                            @endif
                        </div>
                        <div class="product_content">
                            <div class="product_name">
                                <h3>
                                    <a href="{{ route('client.products.detail', $related->id) }}">
                                        {{ $related->name }}
                                    </a>
                                </h3>
                            </div>
                            <div class="product_ratings">
                                <ul>
                                    @php
                                    // Giả sử có hàm đánh giá hoặc trường rating từ 0 đến 5
                                    $rating = $related->rating ?? 0;
                                    @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        <li>
                                        <a href="#">
                                            <i
                                                class="ion-ios-star{{ $i <= $rating ? '' : '-outline' }}"></i>
                                        </a>
                                        </li>
                                        @endfor
                                </ul>
                            </div>
                            <div class="product_footer d-flex align-items-center">
                                @php
                                $prices = $related->getPriceRange();
                                @endphp
                                <div class="price_box">
                                    <span class="current_price">
                                        ${{ number_format($prices->min_price, 0) }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p>Không có sản phẩm liên quan.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!--product area end-->