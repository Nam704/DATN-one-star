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

                        {{-- <div class="tab-pane fade" id="reviews" role="tabpanel">
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
                                            <a href="{{ route('auth.getFormLogin') }}">đăng nhập</a>
                                            để bình luận.
                                        </p>
                                    @endguest
                            </div>
                        </div> --}}
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            <div class="reviews_wrapper">
                                <h2>Tất cả bình luận</h2>
                                <div class="comments_box" id="comments-section">
                                    @foreach ($comments as $comment)
                                        @if ($comment->parent_id === null)
                                            <div class="comment_list" id="comment-{{ $comment->id }}">
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
                                        @endif

                                        @if ($comment->replies->count())
                                            <div class="replies" style="margin-left: 50px;">
                                                @foreach ($comment->replies as $reply)
                                                    <div class="comment_list">
                                                        <div class="comment_thumb">
                                                            <img src="/admin/assets/images/user-201.png" alt="img"
                                                                style="width: 40px; height: 40px;">
                                                        </div>
                                                        <div class="comment_content">
                                                            <div class="comment_meta">
                                                                <h5>{{ $reply->user->name }}</h5>
                                                                <span>{{ $reply->created_at->format('H:i d/m/Y') }}</span>
                                                            </div>
                                                            <p>{{ $reply->comment }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Form thêm bình luận mới --}}
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
                                            <a href="{{ route('auth.getFormLogin') }}">đăng nhập</a>
                                            để bình luận.
                                        </p>
                                    @endguest
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

<script>
    // $(document).ready(function() {
    //     const commentUrl = "{{ route('client.comment-product.add', ['id' => $product->id]) }}";

    //     $('#comment-form').submit(function(e) {
    //         e.preventDefault();

    //         var comment = $('#review_comment').val();

    //         $.ajax({
    //             url: commentUrl,
    //             type: 'POST',
    //             data: {
    //                 comment: comment,
    //                 _token: '{{ csrf_token() }}'
    //             },
    //             success: function(response) {
    //                 $('#comments-section').prepend(`
    //                 <div class="reviews_comment_box">
    //                     <div class="comment_thmb">
    //                         <img src="assets/img/blog/comment2.jpg" alt="">
    //                     </div>
    //                     <div class="comment_text">
    //                         <div class="reviews_meta">
    //                             <p><strong>${response.user_name}</strong> - ${response.created_at}</p>
    //                         </div>
    //                         <p>${response.comment.comment}</p>
    //                     </div>
    //                 </div>
    //             `);

    //                 $('#review_comment').val('');
    //             },
    //             error: function(xhr) {
    //                 console.log("Lỗi: ", xhr.responseText);
    //                 alert('Có lỗi xảy ra, vui lòng thử lại.');
    //             }
    //         });
    //     });
    // });
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
                    const newComment = `
                    <div class="comment_list">
                        <div class="comment_thumb">
                            <img src="/admin/assets/images/user-201.png" alt="img" style="width: 50px; height: 50px;">
                        </div>
                        <div class="comment_content">
                            <div class="comment_meta">
                                <h5>${response.user_name}</h5>
                                <span>${response.created_at}</span>
                            </div>
                            <p>${response.comment.comment}</p>
                        </div>
                    </div>
                `;

                    // Chèn bình luận mới lên đầu
                    $('#comments-section').prepend(newComment);

                    // Xóa nội dung input
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
