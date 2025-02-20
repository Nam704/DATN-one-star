@extends('client.layouts.layout')

<style>
    .variant-card {
        cursor: pointer;
        transition: 0.3s ease;
        border: 2px solid #ddd;
    }

    .variant-card:hover {
        border-color: #007bff;
        transform: scale(1.05);
    }

    .variant-card.active {
        border-color: #007bff;
        box-shadow: 0px 0px 10px rgba(0, 123, 255, 0.5);
    }
</style>

@section('content')

    <!--product details start-->
    <div class="product_details mt-20">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="product-details-tab">
                        <div id="img-1" class="zoomWrapper single-zoom">
                            <a href="#">
                                <img id="main-image" src="{{ Storage::url('images/' . $product->image_primary) }}"
                                    data-zoom-image="{{ Storage::url('images/' . $product->image_primary) }}"
                                    alt="big-1">
                            </a>
                        </div>
                        <div class="single-zoom-thumb">
                            <ul class="s-tab-zoom owl-carousel single-product-active" id="gallery_01">
                                <li>
                                    <img class="album-images" id="main-image"
                                        src="{{ Storage::url('images/' . $product->image_primary) }}"
                                        data-zoom-image="{{ Storage::url('images/' . $product->image_primary) }}"
                                        alt="big-1">
                                </li>
                                @foreach ($albums as $album)
                                    <li>

                                        <img class="album-images" src="{{ Storage::url('images/' . $album->image_path) }}"
                                            alt="zo-th-1" />

                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="product_d_right">
                        <form action="{{ route('client.product.add-to-cart') }}" id="add-to-cart-form" method="POST">
                            @csrf
                            <h1>{{ $product->name }}</h1>

                            <div class="price_box">
                                <span class="current_price" id="current-price">${{ $variants->first()->price }}</span>
                            </div>
                            <div class="product_variant_options">
                                <div class="row">
                                    @php
                                        // Nhóm các giá trị thuộc tính theo attribute_id
                                        $groupedAttributes = [];
                                        foreach ($variants as $variant) {
                                            foreach ($variant->attributes as $attribute) {
                                                $attributeId = $attribute->attributeValue->attribute->id;
                                                $attributeName = $attribute->attributeValue->attribute->name;
                                                $attributeValue = $attribute->attributeValue->value;

                                                if (!isset($groupedAttributes[$attributeId])) {
                                                    $groupedAttributes[$attributeId] = [
                                                        'name' => $attributeName,
                                                        'values' => [],
                                                    ];
                                                }
                                                // Thêm giá trị vào nhóm nếu chưa tồn tại
                                                if (
                                                    !in_array(
                                                        $attributeValue,
                                                        $groupedAttributes[$attributeId]['values'],
                                                    )
                                                ) {
                                                    $groupedAttributes[$attributeId]['values'][] = $attributeValue;
                                                }
                                            }
                                        }
                                    @endphp
                                    @foreach ($groupedAttributes as $attributeId => $attributeData)
                                        <div class="col-md-12 mb-3">
                                            <label><strong>{{ $attributeData['name'] }}</strong></label>
                                            <div class="attribute-values">
                                                @foreach ($attributeData['values'] as $value)
                                                    <button type="button"
                                                        class="btn btn-outline-warning attribute-value-btn"
                                                        data-attribute-id="{{ $attributeId }}"
                                                        data-attribute-value="{{ $value }}">
                                                        {{ $value }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <input type="hidden" name="selected_variant_id" id="selected-variant-id"
                                value="{{ $variants->first()->id }}">
                            <div class="product_variant quantity mt-3">
                                <label>Số lượng</label>
                                <input name="quantity" min="1" max="100" value="1" type="number"
                                    id="quantity">
                                <button type="submit" id="add-to-cart-btn">Thêm vào giỏ hàng</button>
                                <button type="button" id="buy-now-btn">Mua ngay</button>
                            </div>
                            <div class="product_meta">
                                <span>Danh mục: <a href="#">{{ $product->category->name }}</a></span>
                            </div>
                        </form>
                        <div class="product_social">
                            <ul>
                                <li><a class="facebook" href="#" title="facebook"><i class="fa fa-facebook"></i>
                                        Facebook</a></li>
                                <li><a class="twitter" href="#" title="twitter"><i class="fa fa-twitter"></i>
                                        Tweet</a></li>
                                <li><a class="pinterest" href="#" title="pinterest"><i class="fa fa-pinterest"></i>
                                        Save</a></li>
                                <li><a class="google-plus" href="#" title="google +"><i class="fa fa-google-plus"></i>
                                        Share</a></li>
                                <li><a class="linkedin" href="#" title="linkedin"><i class="fa fa-linkedin"></i>
                                        Linked</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--product details end-->

    <!--product info start-->
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
                                {{-- <li>
                                    <a data-toggle="tab" href="#sheet" role="tab" aria-controls="sheet"
                                        aria-selected="false">Specification</a>
                                </li> --}}
                                <li>
                                    <a data-toggle="tab" href="#reviews" role="tab" aria-controls="reviews"
                                        aria-selected="false">Đánh giá (1)</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="info" role="tabpanel">
                                <div class="product_info_content">
                                    {{ $product->description }}
                                </div>
                            </div>
                            {{-- <div class="tab-pane fade" id="sheet" role="tabpanel">
                                <div class="product_d_table">
                                    <form action="#">
                                        <table>
                                            <tbody>
                                                <tr>
                                                    <td class="first_child">Compositions</td>
                                                    <td>Polyester</td>
                                                </tr>
                                                <tr>
                                                    <td class="first_child">Styles</td>
                                                    <td>Girly</td>
                                                </tr>
                                                <tr>
                                                    <td class="first_child">Properties</td>
                                                    <td>Short Dress</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                                <div class="product_info_content">
                                    <p>Fashion has been creating well-designed collections since 2010. The brand offers
                                        feminine designs delivering stylish separates and statement dresses which have since
                                        evolved into a full ready-to-wear collection in which every item is a vital part of
                                        a woman's wardrobe. The result? Cool, easy, chic looks with youthful elegance and
                                        unmistakable signature style. All the beautiful pieces are made in Italy and
                                        manufactured with the greatest attention. Now Fashion extends to a range of
                                        accessories including shoes, hats, belts and more!</p>
                                </div>
                            </div> --}}

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
    <!--product info end-->

    <!--product area start-->


    <section class="product_area mb-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Tiêu đề -->
                    <div class="text-center mb-4">
                        <h2>
                            Sản phẩm liên quan
                        </h2>
                    </div>


                    @if ($relatedProducts->count() > 0)
                        <div class="row">
                            @foreach ($relatedProducts as $relatedProduct)
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100">
                                        <!-- Hình ảnh sản phẩm -->
                                        <a href="{{ route('client.product.detail', $relatedProduct->id) }}">
                                            <img src="{{ Storage::url('images/' . $relatedProduct->image_primary) }}"
                                                class="card-img-top" alt="{{ $relatedProduct->name }}">
                                        </a>

                                        <!-- Giảm giá -->
                                        @if ($relatedProduct->discount > 0)
                                            <div class="badge bg-danger position-absolute top-0 start-0 m-2">
                                                -{{ $relatedProduct->price }}%
                                            </div>
                                        @endif

                                        <!-- Nội dung sản phẩm -->
                                        <div class="card-body">
                                            <!-- Tên sản phẩm -->
                                            <h5 class="card-title">
                                                <a href="{{ route('client.product.detail', $relatedProduct->id) }}"
                                                    class="text-decoration-none text-dark">
                                                    {{ $relatedProduct->name }}
                                                </a>
                                            </h5>

                                            <!-- Danh mục -->
                                            <p class="card-text text-muted">
                                                {{ $relatedProduct->category->name ?? 'Uncategorized' }}
                                            </p>

                                            {{-- <!-- Đánh giá -->
                                            <div class="mb-2">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star {{ $i <= $relatedProduct->rating ? 'text-warning' : 'text-secondary' }}"></i>
                                                @endfor
                                            </div>

                                            <!-- Giá -->
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if ($relatedProduct->discount > 0)
                                                    <span class="text-danger fw-bold">
                                                        ${{ $relatedProduct->price - ($relatedProduct->price * $relatedProduct->discount) / 100 }}
                                                    </span>
                                                    <span class="text-muted text-decoration-line-through">
                                                        ${{ $relatedProduct->price }}
                                                    </span>
                                                @else
                                                    <span class="fw-bold">
                                                        ${{ $relatedProduct->price }}
                                                    </span>
                                                @endif
                                            </div> --}}
                                        </div>

                                        <!-- Nút thêm vào giỏ hàng -->

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            Không có sản phẩm liên quan nào được tìm thấy
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!--product area end-->


@endsection

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // xử lí ảnh
        const mainImage = document.getElementById("main-image");
        const albumImages = document.querySelectorAll(".album-images");
        console.log(albumImages);


        Array.from(albumImages).forEach(image => {
            console.log(image);

            image.addEventListener("click", function() {
                mainImage.src = this.src;
            });
        });

        // Xử lý thuộc tính

        const attributeButtons = document.querySelectorAll('.attribute-value-btn');
        const selectedVariantIdInput = document.getElementById('selected-variant-id');
        const currentPriceElement = document.getElementById('current-price');
        // const oldPriceElement = document.getElementById('old-price');
        const mainImageElement = document.getElementById('main-image'); // Ảnh chính


        // Lưu trữ các giá trị đã chọn
        let selectedAttributes = {};

        // Chuyển đổi dữ liệu variants từ server thành JSON
        const variants = @json($variants);

        // Lấy số lượng thuộc tính của sản phẩm
        const totalAttributes = Object.keys(@json($groupedAttributes)).length;

        // Hàm để kiểm tra xem một giá trị thuộc tính có thể ghép cặp với các giá trị đã chọn hay không
        function isAttributeValueCompatible(attributeId, attributeValue) {
            const tempSelectedAttributes = {
                ...selectedAttributes,
                [attributeId]: attributeValue
            };
            return variants.some(variant => {
                return variant.attributes.every(attribute => {
                    const attrId = attribute.attribute_value.attribute.id;
                    const attrValue = attribute.attribute_value.value;
                    return tempSelectedAttributes[attrId] === undefined ||
                        tempSelectedAttributes[attrId] === attrValue;
                });
            });
        }

        // Hàm để cập nhật trạng thái của các nút giá trị thuộc tính
        function updateAttributeButtons() {
            attributeButtons.forEach(button => {
                const attributeId = button.getAttribute('data-attribute-id');
                const attributeValue = button.getAttribute('data-attribute-value');

                if (selectedAttributes[attributeId] === attributeValue) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }

                // Kiểm tra xem giá trị thuộc tính có thể ghép cặp với các giá trị đã chọn hay không
                if (isAttributeValueCompatible(attributeId, attributeValue)) {
                    button.style.display = 'inline-block'; // Hiện nút
                } else {
                    button.style.display = 'none'; // Ẩn nút
                }
            });
        }

        // Hàm để cập nhật giá và ảnh khi đủ cặp thuộc tính được chọn
        function updatePriceAndImagesIfComplete() {
            if (Object.keys(selectedAttributes).length === totalAttributes) {
                const selectedVariant = findVariantByAttributes(selectedAttributes);
                if (selectedVariant) {

                    console.log(selectedVariant);

                    // Cập nhật giá
                    currentPriceElement.textContent = `$${selectedVariant.price}`;
                    // oldPriceElement.textContent = `$${selectedVariant.price + 10}`;

                    // Cập nhật ảnh chính

                    const mainImageUrl = selectedVariant.images[0]?.url; // Ảnh đầu tiên
                    mainImageElement.src = `{{ Storage::url('images/') }}${mainImageUrl}`;
                    console.log('checkk');

                    mainImageElement.setAttribute('data-zoom-image',
                        `{{ Storage::url('images/') }}${mainImageUrl}`);

                }
            } else {
                // Nếu chưa đủ cặp, giữ nguyên giá và ảnh ban đầu
                const defaultVariant = variants[0];
                currentPriceElement.textContent = `$${defaultVariant.price}`;
                // oldPriceElement.textContent = `$${defaultVariant.price + 10}`;
            }
        }

        attributeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const attributeId = this.getAttribute('data-attribute-id');
                const attributeValue = this.getAttribute('data-attribute-value');

                // Đảm bảo mỗi hàng thuộc tính chỉ chọn một giá trị
                selectedAttributes[attributeId] = attributeValue;

                // Cập nhật trạng thái nút
                updateAttributeButtons();

                // Cập nhật giá và ảnh nếu đủ cặp thuộc tính được chọn
                updatePriceAndImagesIfComplete();

                // Tìm biến thể phù hợp với các giá trị đã chọn
                const selectedVariant = findVariantByAttributes(selectedAttributes);

                if (selectedVariant) {
                    selectedVariantIdInput.value = selectedVariant.id;
                } else {
                    // Nếu không tìm thấy biến thể phù hợp, đặt giá trị mặc định
                    selectedVariantIdInput.value = '';
                }
            });
        });

        function findVariantByAttributes(selectedAttributes) {
            return variants.find(variant => {
                return variant.attributes.every(attribute => {
                    const attributeId = attribute.attribute_value.attribute.id;
                    const attributeValue = attribute.attribute_value.value;
                    return selectedAttributes[attributeId] === attributeValue;
                });
            });
        }

        // Khởi tạo trạng thái ban đầu
        updateAttributeButtons();
        updatePriceAndImagesIfComplete();


        document.getElementById('buy-now-btn').addEventListener('click', function() {
            const selectedVariantId = document.getElementById('selected-variant-id').value;
            const quantity = document.getElementById('quantity').value;
            window.location.href =
                `{{ route('client.product.checkout') }}?variant_id=${selectedVariantId}&quantity=${quantity}`;
        });




    });
</script>


<script></script>
