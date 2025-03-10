@extends('admin.layouts.layout')
@section('content')
<style>
    .product-details-tab img {
        max-width: 100%;
        height: auto;
    }

    .variant-image {
        width: 80px;
        height: 80px;
        object-fit: contain;
    }

    .single-zoom-thumb img {
        width: 70px;
        height: 70px;
        object-fit: cover;
    }

    .product-album {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding: 10px 0;
    }

    .product-album img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        cursor: pointer;
    }
</style>
<div class="container-fluid mt-3">
    <div class="product_details ">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4">
                    <div class="product-details-tab">
                        <div id="img-1" class="zoomWrapper single-zoom">
                            <a href="#">
                                <img id="zoom1" src="{{ asset( $product->image_primary) }}"
                                    data-zoom-image="{{ asset( $product->image_primary) }}" alt="{{ $product->name }}">
                            </a>
                        </div>
                        <div class="product-album d-flex justify-content-start">
                            @foreach($product->product_albums as $album)
                            <a href="#" class="elevatezoom-gallery" data-update=""
                                data-image="{{ asset( $album->image_path) }}">
                                <img src="{{ asset( $album->image_path) }}" alt="Gallery">
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8">
                    <div class="product_d_right">
                        <h1>{{ $product->name }}</h1>

                        <p><strong>Thương hiệu:</strong> {{ $product->brand->name }}</p>
                        <p><strong>Danh mục:</strong> {{ $product->category->name }}</p>
                        <div class="product_variant">
                            <h3>Biến thể sản phẩm</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Hình ảnh</th>
                                        <th>Thuộc tính</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->variants as $variant)
                                    <tr>
                                        <td>{{ $variant['sku'] }}</td>
                                        <td>${{ number_format((float)$variant['price'], 2) }}</td>
                                        <td>{{ $variant['quantity'] > 0 ? $variant['quantity'] : 'Hết hàng' }}</td>
                                        <td>
                                            @if(!empty($variant['image']))
                                            <img src="{{ asset($variant['image']) }}" alt="Variant Image"
                                                class="variant-image">
                                            @else
                                            <img src="{{ asset('storage/default-image.png') }}" alt="No Image"
                                                class="variant-image">
                                            @endif
                                        </td>
                                        <td>
                                            <ul>
                                                @foreach($variant['values'] as $attr)
                                                <li><strong>{{ $attr['name'] }}:</strong> {{ $attr['value'] }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">


                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="header-title">Thông tin chi tiết</h4>

                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills bg-nav-pills nav-justified mb-3">
                                <li class="nav-item">
                                    <a href="#home1" data-bs-toggle="tab" aria-expanded="false"
                                        class="nav-link rounded-0">
                                        Mô tả
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#profile1" data-bs-toggle="tab" aria-expanded="true"
                                        class="nav-link rounded-0 active">
                                        Đánh giá
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#settings1" data-bs-toggle="tab" aria-expanded="false"
                                        class="nav-link rounded-0">
                                        Bình luận
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div class="tab-pane" id="home1">
                                    {!! $product->description !!}
                                </div>
                                <div class="tab-pane show active" id="profile1">
                                    Chưa có đánh giá
                                </div>
                                <div class="tab-pane" id="settings1">
                                    Chưa có bình luận
                                </div>
                            </div>
                        </div> <!-- end card-body -->
                    </div> <!-- end card-->
                </div> <!-- end col -->
            </div>
        </div>
    </div>
</div>
@endsection