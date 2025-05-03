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
                                    aria-selected="false">Bình luận ({{ $totalComments }})</a>
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
                                <h2>Tất cả bình luận</h2>
                                <div class="comments_box">
                                    @foreach ($comments as $comment)
                                        <div class="comment_list">
                                            <div class="comment_thumb">
                                                <img src="/admin/assets/images/user-201.png" alt="imgimg"
                                                    style="width: 50px; height: 50px;">
                                            </div>
                                            <div class="comment_content">
                                                <div class="comment_meta">
                                                    <h5>{{ $comment->user->name }}</h5>
                                                    <span>{{ $comment->created_at->format('H:i d/m/Y') }}</span>
                                                </div>
                                                <p>{{ $comment->comment }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="product_review_form">
                                    @auth
                                        <form id="comment-form"
                                            action="{{ route('client.comment-product.add', ['id' => $product->id]) }}"
                                            method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12">
                                                    <textarea name="comment" id="review_comment" required placeholder="Nội dung bình luận"></textarea>
                                                </div>
                                            </div>
                                            <button type="submit">Gửi bình luận</button>
                                        </form>
                                    @endauth

                                    @guest
                                        <p>Bạn phải
                                            <a href="{{ route('login') }}">đăng nhập</a>
                                            để bình luận.
                                        </p>
                                    @endguest

                                </div>
                            </div>
                        </div>
                        <button type="submit">Gửi bình luận</button>
                        </form>
                    </div>
                @else
                    <p class="mt-3"></p>
                    @endif
                @else
                    <p class="mt-3">Vui lòng <a href="{{ route('auth.getFormLogin') }}">đăng nhập</a> để bình luận.
                    </p>
                @endauth
            </div>
        </div>

        {{-- Script --}}
        <script>
            // Chọn số sao
            document.querySelectorAll('.star_rating_input i').forEach(function(star) {
                star.addEventListener('click', function() {
                    let rating = this.getAttribute('data-value');
                    document.getElementById('rating_input').value = rating;

                    document.querySelectorAll('.star_rating_input i').forEach(function(s) {
                        s.classList.remove('checked');
                    });
                    for (let i = 0; i < rating; i++) {
                        document.querySelectorAll('.star_rating_input i')[i].classList.add('checked');
                    }
                });
            });

            // Gửi form bằng Ajax
            document.getElementById('comment_form')?.addEventListener('submit', function(e) {
                e.preventDefault();

                if (!{{ auth()->check() ? 'true' : 'false' }}) {
                    alert('Vui lòng đăng nhập để gửi bình luận.');
                    window.location.href = '{{ route('auth.getFormLogin') }}'; // Chuyển hướng đến trang đăng nhập
                    return;
                }

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
                        alert(data.message);
                        const userName = '{{ auth()->user() ? auth()->user()->name : 'Người dùng' }}';
                        // Tạo HTML mới cho bình luận
                        const newComment = `
                <div class="reviews_comment_box">
                    <div class="comment_thmb">
              <img  src="{{ asset(auth()->user()->profile_image ? 'storage/' . auth()->user()->profile_image : 'admin/assets/images/user-201.png') }}"  alt="Avatar"
                  style="width: 60px; height: 60px; object-fit: cover;">
                    </div>
                    <div class="comment_text">
                        <div class="reviews_meta">
                            <div class="star_rating">
                                <ul>
                                    ${[...Array(5)].map((_, i) =>
                                        `<li><i class="ion-ios-star${i < formData.get('rating') ? '' : '-outline'}"></i></li>`
                                    ).join('')}
                                </ul>
                            </div>
                            <p><strong>${userName}</strong> - hôm nay</p>
                            <span>${formData.get('comment')}</span>
                        </div>
                    </div>
                </div>`;

                        // Thêm bình luận mới vào dưới cùng
                        document.getElementById('comment_list').insertAdjacentHTML('beforeend', newComment);

                        // Cập nhật số lượng
                        let totalElem = document.getElementById('total_comments');
                        let currentCount = parseInt(totalElem.textContent);
                        totalElem.textContent = (currentCount + 1) + ' đánh giá cho {{ $product->name }}';

                        // Ẩn form sau khi gửi thành công
                        document.querySelector('.product_review_form')?.remove();
                        document.querySelector('.product_ratting')?.remove();
                        document.querySelector('.comment_title')?.remove();

                        const note = document.createElement('p');
                        note.textContent = '';
                        document.querySelector('.reviews_wrapper').appendChild(note);
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
                color: #f5c518 !important;
                /* vàng */
            }

            .star_rating ul li i,
            .reviews_meta .ion-ios-star {
                color: #f5c518 !important;
            }

            .reviews_meta .ion-ios-star-outline {
                color: #ddd !important;
            }
        </style>



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

<script>
    $(document).ready(function() {
        const commentUrl = "{{ route('client.comment-product.add', ['id' => $product->id]) }}";

        $('#comment-form').submit(function(e) {
            e.preventDefault();

            var comment = $('#review_comment').val();

            $.ajax({
                url: commentUrl,
                type: 'POST',
                data: {
                    comment: comment,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#comments-section').prepend(`
                    <div class="reviews_comment_box">
                        <div class="comment_thmb">
                            <img src="assets/img/blog/comment2.jpg" alt="">
                        </div>
                        <div class="comment_text">
                            <div class="reviews_meta">
                                <p><strong>${response.user_name}</strong> - ${response.created_at}</p>
                            </div>
                            <p>${response.comment.comment}</p>
                        </div>
                    </div>
                `);

                    $('#review_comment').val('');
                },
                error: function(xhr) {
                    console.log("Lỗi: ", xhr.responseText);
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            });
        });
    });
</script>
