@extends('client.layouts.layout')
@section('content')
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-danger">
    {{ session('error') }}
</div>
@endif

    <div class="row">
        <!-- Sidebar: Bộ lọc -->
        <div class="col-lg-3 col-md-12">
            <aside class="sidebar_widget">
                <div class="widget_inner">
                    <div class="widget_list widget_filter">
                        <h2>Lọc theo giá</h2>
                        <form method="GET" action="{{ route('client.shop') }}">
                            <!-- Thanh trượt cho khoảng giá -->
                            <div id="slider-range"></div>
                            <div class="price-display">
                                <input type="text" name="min_price" id="min-price" placeholder="Giá tối thiểu"
                                    value="{{ old('min_price', request('min_price', 0)) }}">
                                <input type="text" name="max_price" id="max-price" placeholder="Giá tối đa"
                                    value="{{ old('max_price', request('max_price', 50000000)) }}">
                            </div>
                        </form>
                    </div>
                    <div class="widget_list widget_categories" id="filters">
                        <form id="filter-form" method="GET" action="{{ route('client.shop') }}">
                            <!-- Bộ lọc theo danh mục -->
                            <div class="widget_list widget_categories">
                                <div id="categories">
                                    <h2>Categories</h2>
                                    <ul>
                                        @foreach ($categories as $category)
                                            <li>
                                                <input type="checkbox" name="categories[]" class="category-filter"
                                                    value="{{ $category->id }}"
                                                    @if (in_array($category->id, old('categories', []))) checked @endif>
                                                {{ $category->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <!-- Bộ lọc theo thương hiệu -->
                            <div class="widget_list widget_categories">
                                <div id="brands">
                                    <h3>Brands</h3>
                                    <ul>
                                        @foreach ($brands as $brand)
                                            <li>
                                                <input type="checkbox" name="brands[]" class="brand-filter"
                                                    value="{{ $brand->id }}"
                                                    @if (in_array($brand->id, old('brands', []))) checked @endif>
                                                {{ $brand->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </aside>
        </div>
        <!-- Nội dung sản phẩm -->
        <div class="col-lg-9 col-md-12">
            {{-- product-list.blade.php --}}
            <div class="shop_banner">
                <img src="assets/img/bg/banner8.jpg" alt="">
            </div>
            <div class="shop_title">
                <h1>shop</h1>
            </div>
            <div class="shop_toolbar_wrapper">
                <div class="shop_toolbar_btn">
                    <button data-role="grid_3" type="button" class="active btn-grid-3" data-toggle="tooltip"
                        title="3"></button>
                    <button data-role="grid_4" type="button" class="btn-grid-4" data-toggle="tooltip"
                        title="4"></button>
                    <button data-role="grid_list" type="button" class="btn-list" data-toggle="tooltip"
                        title="List"></button>
                </div>
                <div class="niceselect_option">
                    <form class="select_option" action="#">
                        <select name="orderby" id="short">
                            <option selected value="1">Sort by average rating</option>
                            <option value="2">Sort by popularity</option>
                            <option value="3">Sort by newness</option>
                            <option value="4">Sort by price: low to high</option>
                            <option value="5">Sort by price: high to low</option>
                            <option value="6">Product Name: A→Z</option>
                            <option value="7">Product Name: Z→A</option>
                        </select>
                    </form>
                </div>


                <div class="page_amount">
                    <p>Showing 1–9 of 21 results</p>
                </div>
            </div>
            <div class="row shop_wrapper" id="product-list">
                @include('client.shops.product-list')
            </div>

            <div class="shop_toolbar t_bottom" id="pagination">
                @include('client.shops.pagination')
            </div>

        </div>
    </div>
@endsection
@section('scripts')
    <script>
        // Hàm định dạng số: chuyển số nguyên thành chuỗi với dấu chấm phân cách (ví dụ: 50000000 => 50.000.000)
        function formatNumber(num) {
            num = parseInt(num) || 0;
            return num.toLocaleString('vi-VN');
        }

        // Hàm gửi dữ liệu lọc sản phẩm qua AJAX
        function fetchFilteredProducts() {
            let params = new URLSearchParams();

            // Lấy danh sách category được chọn
            document.querySelectorAll('.category-filter').forEach(function(el) {
                if (el.checked) {
                    params.append('categories[]', el.value);
                }
            });
            // Lấy danh sách brand được chọn
            document.querySelectorAll('.brand-filter').forEach(function(el) {
                if (el.checked) {
                    params.append('brands[]', el.value);
                }
            });
            // Lấy giá trị khoảng giá từ input, loại bỏ dấu phân cách
            let minPrice = document.getElementById('min-price').value.replace(/\./g, '');
            let maxPrice = document.getElementById('max-price').value.replace(/\./g, '');
            if (minPrice) params.append('min_price', minPrice);
            if (maxPrice) params.append('max_price', maxPrice);

            // Lấy giá trị sắp xếp nếu có
            let sortEl = document.getElementById('short');
            if (sortEl && sortEl.value) {
                params.append('orderby', sortEl.value);
            }

            fetch('{{ route('client.filter') }}?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    document.getElementById('product-list').innerHTML = data.products;
                    document.getElementById('pagination').innerHTML = data.pagination;
                })
                .catch(error => console.error('Error:', error));
        }

        $(document).ready(function() {
            // Khởi tạo slider với giá trị từ 0 đến 50.000.000
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 50000000,
                step: 100000,
                values: [0, 50000000],
                slide: function(event, ui) {
                    // Cập nhật hiển thị khoảng giá với định dạng số có dấu phân cách
                    $("#amount").val(formatNumber(ui.values[0]) + " - " + formatNumber(ui.values[1]));
                    // Cập nhật giá trị cho các input hiển thị (có định dạng)
                    $("#min-price").val(formatNumber(ui.values[0]));
                    $("#max-price").val(formatNumber(ui.values[1]));
                    fetchFilteredProducts();
                }
            });
            // Thiết lập hiển thị ban đầu cho slider và input
            $("#amount").val(
                formatNumber($("#slider-range").slider("values", 0)) +
                " - " +
                formatNumber($("#slider-range").slider("values", 1))
            );
            $("#min-price").val(formatNumber($("#slider-range").slider("values", 0)));
            $("#max-price").val(formatNumber($("#slider-range").slider("values", 1)));

            // Khi người dùng nhập tay vào input tối thiểu
            $('#min-price').on('input', function() {
                let rawVal = $(this).val().replace(/\./g, '');
                let numeric = parseInt(rawVal) || 0;
                $(this).val(formatNumber(numeric));
                $("#slider-range").slider("values", 0, numeric);
                fetchFilteredProducts();
            });

            // Khi người dùng nhập tay vào input tối đa
            $('#max-price').on('input', function() {
                let rawVal = $(this).val().replace(/\./g, '');
                let numeric = parseInt(rawVal) || 50000000;
                $(this).val(formatNumber(numeric));
                $("#slider-range").slider("values", 1, numeric);
                fetchFilteredProducts();
            });

            // Lắng nghe sự thay đổi của checkbox category và brand
            document.querySelectorAll('.category-filter').forEach(function(el) {
                el.addEventListener('change', fetchFilteredProducts);
            });
            document.querySelectorAll('.brand-filter').forEach(function(el) {
                el.addEventListener('change', fetchFilteredProducts);
            });
            // Lắng nghe sự thay đổi của dropdown sắp xếp (nếu có)
            let sortSelect = document.getElementById('short');
            if (sortSelect) {
                sortSelect.addEventListener('change', fetchFilteredProducts);
            }
        });
    </script>
@endsection
