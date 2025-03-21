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
            <div class="container bg-white">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="product-details-tab">
                            <div id="img-1" class="zoomWrapper single-zoom">
                                <a href="#">
                                    <img id="zoom1" src="{{ asset($product->image_primary) }}"
                                        data-zoom-image="{{ asset($product->image_primary) }}" alt="{{ $product->name }}">
                                </a>
                            </div>
                            <div class="product-album d-flex justify-content-start">
                                @foreach ($product->product_albums as $album)
                                    <a href="#" class="elevatezoom-gallery" data-update=""
                                        data-image="{{ asset($album->image_path) }}">
                                        <img src="{{ asset($album->image_path) }}" alt="Gallery">
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
                                <table class="table table-bordered" id="table1">
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
                                        @foreach ($product->variants as $variant)
                                            <tr>
                                                <td>{{ $variant['sku'] }}</td>
                                                <td>${{ number_format((float) $variant['price'], 2) }}</td>
                                                <td>{{ $variant['quantity'] > 0 ? $variant['quantity'] : 'Hết hàng' }}</td>
                                                <td>
                                                    @if (!empty($variant['image']))
                                                        <img src="{{ asset($variant['image']) }}" alt="Variant Image"
                                                            class="variant-image">
                                                    @else
                                                        <img src="{{ asset('storage/default-image.png') }}" alt="No Image"
                                                            class="variant-image">
                                                    @endif
                                                </td>
                                                <td>
                                                    <ul>
                                                        @foreach ($variant['values'] as $attr)
                                                            <li><strong>{{ $attr['name'] }}:</strong> {{ $attr['value'] }}
                                                            </li>
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
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="header-title">Thống kê đơn hàng</h4>
                            </div>
                            <div class="card-body">
                                <h5>Trạng thái đơn hàng</h5>
                                <table class="table table-bordered" id="table2">
                                    <thead>
                                        <tr>
                                            <th>Trạng thái</th>
                                            <th>Số lượng đơn</th>
                                            <th>Tổng giá trị</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orderStatusStats as $stat)
                                            <tr>
                                                <td>{{ $stat['status_name'] }}</td>
                                                <td>{{ $stat['total_orders'] }}</td>
                                                <td>${{ number_format((float) $stat['total_amount'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                <h5>Thống kê biến thể sản phẩm</h5>
                                <!-- Trong bảng "Thống kê biến thể sản phẩm" -->
                                <table class="table table-bordered" id="table3">
                                    <thead>
                                        <tr>
                                            <th>SKU</th>
                                            <th>Số lượng đơn</th>
                                            <th>Tổng số lượng</th>
                                            <th>Tổng giá trị</th>
                                            <th>Hành động</th> <!-- Thêm cột hành động -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($variantStats as $variantId => $stat)
                                            @php
                                                $variant = $product->variants->firstWhere('id', $variantId);
                                            @endphp
                                            <tr>
                                                <td>{{ $variant['sku'] }}</td>
                                                <td>{{ $stat['total_orders'] }}</td>
                                                <td>{{ $stat['total_quantity'] }}</td>
                                                <td>${{ number_format((float) $stat['total_amount'], 2) }}</td>
                                                <td>
                                                    <!-- Nút "Xem chi tiết" -->
                                                    <a href="{{ route('admin.products.product-variant-detail', ['productId' => $product->id, 'variantId' => $variantId]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Xem chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#table1').DataTable({
                responsive: true,
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Hiển thị trang _PAGE_ của _PAGES_",
                    "infoEmpty": "Không có dữ liệu",
                    "infoFiltered": "(lọc từ _MAX_ bản ghi)",
                    "search": "Tìm kiếm:",
                },
                "pageLength": 5,
                "bLengthChange": false,
                "searching": false,
            });
            $('#table2').DataTable({
                responsive: true,
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Hiển thị trang _PAGE_ của _PAGES_",
                    "infoEmpty": "Không có dữ liệu",
                    "infoFiltered": "(lọc từ _MAX_ bản ghi)",
                    "search": "Tìm kiếm:",
                },
                "pageLength": 3,
                "bLengthChange": false,
                "searching": false,
            });
            $('#table3').DataTable({
                responsive: true,
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ bản ghi mỗi trang",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Hiển thị trang _PAGE_ của _PAGES_",
                    "infoEmpty": "Không có dữ liệu",
                    "infoFiltered": "(lọc từ _MAX_ bản ghi)",
                    "search": "Tìm kiếm:",
                },
                "pageLength": 5,
                "bLengthChange": false,
                "searching": false,
            });
        });
    </script>
@endpush
