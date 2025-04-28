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

    @if($comments->count() > 0)
    <h2>{{ $comments->count() }} đánh giá cho {{ $product->name }}</h2>
    @foreach($comments as $comment)
        <div class="reviews_comment_box">
            <div class="comment_thmb">  
            <img src="{{ asset('storage/' . ($comment->user->profile_image ?? 'admin/assets/images/user-201.png')) }}" 
    alt="Avatar" style="width: 60px; height: 60px; object-fit: cover;">

            </div>
            <div class="comment_text">
                <div class="reviews_meta">
                    <div class="star_rating">
                        <ul>
                            @for($i = 1; $i <= 5; $i++)
                                <li>
                                    <a href="#">
                                        <i class="ion-ios-star{{ $i <= $comment->rating ? '' : '-outline' }}"></i>
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                    <p><strong>{{ $comment->user->name ?? 'Người dùng' }}</strong> - {{ $comment->created_at->format('d/m/Y') }}</p>
                    <span>{{ $comment->comment }}</span>
                </div>
            </div>
        </div>
    @endforeach
@else
    <h2>Chưa có bình luận nào</h2>
@endif


        {{-- Form gửi bình luận --}}
        @auth
            <div class="comment_title mt-4">
                <h2>Thêm đánh giá của bạn</h2>
            </div>

            <div class="product_ratting mb-10">
                <h3>Đánh giá của bạn</h3>
                <ul class="star_rating_input">
                    @for($i = 1; $i <= 5; $i++)
                        <li><i class="fa fa-star" data-value="{{ $i }}"></i></li>
                    @endfor
                </ul>
            </div>

            <div class="product_review_form">
                <form id="comment_form" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="rating" id="rating_input" value="5"> {{-- Mặc định 5 sao --}}

                    <div class="row">
                        <div class="col-12">
                            <label for="review_comment">Nội dung bình luận</label>
                            <textarea name="comment" id="review_comment" required></textarea>
                        </div>
                    </div>
                    <button type="submit">Gửi bình luận</button>
                </form>
            </div>

            {{-- Script chọn số sao --}}
            <script>
                document.querySelectorAll('.star_rating_input i').forEach(function(star) {
                    star.addEventListener('click', function() {
                        let rating = this.getAttribute('data-value');
                        document.getElementById('rating_input').value = rating;

                        // Highlight lại các sao
                        document.querySelectorAll('.star_rating_input i').forEach(function(s) {
                            s.classList.remove('checked');
                        });
                        for (let i = 0; i < rating; i++) {
                            document.querySelectorAll('.star_rating_input i')[i].classList.add('checked');
                        }
                    });
                });
        // Script gửi form bằng Ajax
                document.getElementById('comment_form').addEventListener('submit', function(e) {
            e.preventDefault(); // Chặn hành động mặc định

            let formData = new FormData(this);

            fetch('{{ route('client.products.storecomment') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message); // Thông báo thành công
                document.getElementById('comment_form').reset(); // Reset form
                // Reset highlight sao về mặc định 5 sao
                document.getElementById('rating_input').value = 5;
                document.querySelectorAll('.star_rating_input i').forEach(function(s) {
                    s.classList.remove('checked');
                });
                for (let i = 0; i < 5; i++) {
                    document.querySelectorAll('.star_rating_input i')[i].classList.add('checked');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Đã có lỗi xảy ra, vui lòng thử lại.');
            });
        });
            </script>

            <style>
                .star_rating_input i {
                    font-size: 24px;
                    color: #ddd;
                    cursor: pointer;
                }
                .star_rating_input i.checked {
                    color: #f5c518;
                }
            </style>

        @else
            <p>Vui lòng <a href="{{ route('auth.getFormLogin') }}">đăng nhập</a> để bình luận.</p>
        @endauth
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