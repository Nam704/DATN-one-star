@extends('client.layouts.layout')
@section('content')
    <div class="row">
        <!-- Sidebar: Bộ lọc -->
        <div class="col-lg-3 col-md-12">
            <aside class="sidebar_widget">
                <div class="widget_inner">
                    <div class="widget_list widget_filter">
                        <h2>Lọc theo giá</h2>
                        <form method="GET" action="{{ route('client.shop') }}">
                            <!-- Chỉ sử dụng input để lọc -->
                            <div class="mb-3">
                                <label for="min-price">Giá thấp:</label>
                                <input type="text" name="min_price" id="min-price"
                                    class="form-contro border border-dark rounded" placeholder="Giá tối thiểu"
                                    value="{{ old('min_price', number_format(request('min_price', 0), 0, ',', '.')) }}">
                            </div>
                            <div class="mb-3">
                                <label for="max-price">Giá cao:</label>
                                <input type="text" name="max_price" id="max-price"
                                    class="form-contro border border-dark rounded" placeholder="Giá tối đa"
                                    value="{{ old('max_price', number_format(request('max_price', 50000000), 0, ',', '.')) }}">
                            </div>
                            <!-- Hiển thị thông báo lỗi ngay dưới bộ lọc giá -->
                            <div id="price-error" style="color: red; margin-top: 5px;"></div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>

<script>
    // Sử dụng Cleave.js để định dạng số ngay khi nhập với onValueChanged callback
    var cleaveMin = new Cleave('#min-price', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.',
        onValueChanged: function(e) {
            if (e.target.rawValue === '') {
                e.target.value = '';
            }
        }
    });

    var cleaveMax = new Cleave('#max-price', {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand',
        numeralDecimalMark: ',',
        delimiter: '.',
        onValueChanged: function(e) {
            if (e.target.rawValue === '') {
                e.target.value = '';
            }
        }
    });

    // Hàm kiểm tra và hiển thị thông báo lỗi nếu giá nhập không hợp lệ
    function validatePrices() {
        // Lấy giá trị chưa được định dạng (dạng số nguyên)
        let minRaw = $('#min-price').val().replace(/\./g, '').replace(/,/g, '');
        let maxRaw = $('#max-price').val().replace(/\./g, '').replace(/,/g, '');
        let minPrice = minRaw === '' ? null : parseInt(minRaw);
        let maxPrice = maxRaw === '' ? null : parseInt(maxRaw);
        let errorMsg = '';

        // Kiểm tra nếu Giá cao vượt quá 50.000.000
        if (maxPrice !== null && maxPrice > 50000000) {
            errorMsg = "Giá cao không được vượt quá 50.000.000";
        }
        // Kiểm tra nếu Giá thấp vượt quá Giá cao
        else if (minPrice !== null && maxPrice !== null && minPrice > maxPrice) {
            errorMsg = "Giá thấp không thể vượt quá Giá cao";
        }

        $('#price-error').text(errorMsg);
        return errorMsg === '';
    }

    // Hàm gửi dữ liệu lọc sản phẩm qua AJAX
    function fetchFilteredProducts() {
        if (!validatePrices()) {
            return;
        }
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
        let minPrice = $('#min-price').val().replace(/\./g, '').replace(/,/g, '');
        let maxPrice = $('#max-price').val().replace(/\./g, '').replace(/,/g, '');
        if (minPrice !== '') params.append('min_price', minPrice);
        if (maxPrice !== '') params.append('max_price', maxPrice);

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
        // Lắng nghe sự thay đổi của input, checkbox, dropdown
        $('#min-price, #max-price').on('input', function() {
            validatePrices();
            fetchFilteredProducts();
        });

        document.querySelectorAll('.category-filter').forEach(function(el) {
            el.addEventListener('change', fetchFilteredProducts);
        });
        document.querySelectorAll('.brand-filter').forEach(function(el) {
            el.addEventListener('change', fetchFilteredProducts);
        });

        let sortSelect = document.getElementById('short');
        if (sortSelect) {
            sortSelect.addEventListener('change', fetchFilteredProducts);
        }
    });
</script>
@endsection
