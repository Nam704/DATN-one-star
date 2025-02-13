@extends('client.layouts.layout')
@section('content')
    <div class="row">
        <!-- Sidebar: Bộ lọc -->
        <div class="col-lg-3 col-md-12">
            <aside class="sidebar_widget">
                <div class="widget_inner">
                    <div class="widget_list widget_filter">
                        <h2>Lọc theo giá</h2>
                        <form method="GET" action="{{ route('auth.shop') }}">
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
                        <form id="filter-form" method="GET" action="{{ route('auth.shop') }}">
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
                            <!-- (Không cần nút lọc vì AJAX được kích hoạt khi thay đổi) -->
                        </form>
                    </div>
                </div>
            </aside>
        </div>
        <!-- Nội dung sản phẩm -->
        <div class="col-lg-9 col-md-12">
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
        // Sử dụng 1 hàm fetchFilteredProducts duy nhất
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
            // Lấy giá trị khoảng giá
            let minPrice = document.getElementById('min-price').value;
            let maxPrice = document.getElementById('max-price').value;
            if (minPrice) params.append('min_price', minPrice.replace(/\./g, ''));
            if (maxPrice) params.append('max_price', maxPrice.replace(/\./g, ''));

            fetch('{{ route('auth.filter') }}?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    document.getElementById('product-list').innerHTML = data.products;
                    document.getElementById('pagination').innerHTML = data.pagination;
                })
                .catch(error => console.error('Error:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            const categoryFilters = document.querySelectorAll('.category-filter');
            const brandFilters = document.querySelectorAll('.brand-filter');
            const minPriceInput = document.getElementById('min-price');
            const maxPriceInput = document.getElementById('max-price');

            // Lắng nghe sự kiện thay đổi trên checkbox và input
            categoryFilters.forEach(function(el) {
                el.addEventListener('change', fetchFilteredProducts);
            });
            brandFilters.forEach(function(el) {
                el.addEventListener('change', fetchFilteredProducts);
            });
            minPriceInput.addEventListener('input', fetchFilteredProducts);
            maxPriceInput.addEventListener('input', fetchFilteredProducts);
        });

        $(function() {
            $("#slider-range").slider({
                range: true,
                min: 0,
                max: 50000000,
                step: 100000,
                values: [0, 50000000],
                slide: function(event, ui) {
                    // Cập nhật hiển thị giá với định dạng có dấu phân cách hàng nghìn
                    $("#amount").val(ui.values[0].toLocaleString('vi-VN') + " - " + ui.values[1]
                        .toLocaleString('vi-VN'));
                    // Cập nhật giá trị cho các input dùng cho AJAX
                    $("#min-price").val(ui.values[0]);
                    $("#max-price").val(ui.values[1]);
                    // Gọi hàm AJAX để cập nhật sản phẩm ngay khi kéo thả
                    fetchFilteredProducts();
                }
            });
            // Thiết lập hiển thị giá ban đầu (nếu cần, thêm phần hiển thị input "amount" vào HTML nếu chưa có)
            $("#amount").val($("#slider-range").slider("values", 0).toLocaleString('vi-VN') +
                " - " + $("#slider-range").slider("values", 1).toLocaleString('vi-VN'));
        });


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

            // Lấy giá trị khoảng giá từ các input ẩn
            let minPrice = document.getElementById('min-price').value;
            let maxPrice = document.getElementById('max-price').value;
            if (minPrice) params.append('min_price', minPrice);
            if (maxPrice) params.append('max_price', maxPrice);

            // Lấy giá trị sắp xếp từ select dropdown
            let orderby = document.getElementById('short').value;
            if (orderby) {
                params.append('orderby', orderby);
            }

            fetch('{{ route('auth.filter') }}?' + params.toString())
                .then(response => response.json())
                .then(data => {
                    document.getElementById('product-list').innerHTML = data.products;
                    document.getElementById('pagination').innerHTML = data.pagination;
                })
                .catch(error => console.error('Error:', error));
        }
        document.addEventListener('DOMContentLoaded', function() {
            const sortSelect = document.getElementById('short');

            sortSelect.addEventListener('change', function() {
                fetchFilteredProducts();
            });

            // Các sự kiện khác của checkbox, input price… (như đã có)
            document.querySelectorAll('.category-filter').forEach(function(el) {
                el.addEventListener('change', fetchFilteredProducts);
            });
            document.querySelectorAll('.brand-filter').forEach(function(el) {
                el.addEventListener('change', fetchFilteredProducts);
            });
            document.getElementById('min-price').addEventListener('input', fetchFilteredProducts);
            document.getElementById('max-price').addEventListener('input', fetchFilteredProducts);
        });
    </script>
@endsection
