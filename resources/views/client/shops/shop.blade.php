@extends('client.layouts.home.layout')
@section('content')
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.2.96/css/materialdesignicons.min.css">

    <style>
        .btn-check:checked+.btn-outline-dark {
            background-color: #0d6efd !important;
            /* primary */
            border-color: #0d6efd !important;
            color: #fff !important;
        }

        .btn-outline-dark:hover {
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
            color: #fff !important;
        }
    </style>
    <div class="shop_area shop_reverse">
        <div class="container">
            <div class="row">
                @php
                    // Nếu controller đã pass mảng, sử dụng luôn; nếu không, fallback về request()->input(...)
                    $selectedCategories = $selectedCategories ?? request()->input('categories', []);
                    $selectedBrands = $selectedBrands ?? request()->input('brands', []);
                    $selectedAttrs = request()->input('attribute_values', []);
                @endphp
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
                                    <div class="d-grid mb-4">
                                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#attributeModal">
                                            <i class="mdi mdi-filter-menu"></i> Lọc thuộc tính
                                        </button>
                                    </div>

                                    <!-- Bộ lọc theo danh mục -->
                                    <div class="widget_list widget_categories">
                                        <div id="categories">
                                            <h2>Danh mục</h2>
                                            <ul>
                                                @foreach ($categories as $category)
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" name="categories[]"
                                                                class="category-filter" value="{{ $category->id }}"
                                                                {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                                                            {{ $category->name }}
                                                        </label>
                                                    </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                    </div>
                                    <!-- Bộ lọc theo thương hiệu -->
                                    <div class="widget_list widget_categories">
                                        <div id="brands">
                                            <h2>Thương hiệu</h2>
                                            <ul>
                                                @foreach ($brands as $brand)
                                                    <li>
                                                        <label>
                                                            <input type="checkbox" name="brands[]" class="brand-filter"
                                                                value="{{ $brand->id }}"
                                                                {{ in_array($brand->id, $selectedBrands) ? 'checked' : '' }}>
                                                            {{ $brand->name }}
                                                        </label>
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
                        <div class="slider_area owl-carousel">
                            @foreach ($banners as $banner)
                                <!-- <div class="single_slider d-flex align-items-center" > -->
                                <div class="slider_content"
                                    style="background-image: url('{{ Storage::url($banner->image) }}');">
                                    <h2>{{ $banner->title }}</h2>
                                    <h1>{{ $banner->description }}</h1>
                                    <a class="button" href="{{ route('client.shop') }}">Shopping Now</a>
                                </div>
                                <!-- </div> -->
                            @endforeach
                        </div>
                    </div>
                    <div class="shop_title">
                        <h1>Sản Phẩm</h1>
                    </div>
                    <div class="shop_toolbar_wrapper">
                        <div class="shop_toolbar_btn">
                            {{-- <button data-role="grid_3" type="button" class="active btn-grid-3" data-toggle="tooltip"
                                title="3"></button> --}}
                            {{-- <button data-role="grid_4" type="button" class="btn-grid-4" data-toggle="tooltip"
                                title="4"></button>
                            <button data-role="grid_list" type="button" class="btn-list" data-toggle="tooltip"
                                title="List"></button> --}}
                        </div>
                        <div class="niceselect_option2">
                            <div class="mb-3">
                                <label for="orderby" class="form-label">Sắp xếp theo</label>
                                <select name="orderby" id="orderby" class="form-select">
                                    <option value="default">Mặc định</option>
                                    <option value="price_asc">Giá: thấp đến cao</option>
                                    <option value="price_desc">Giá: cao đến thấp</option>
                                    <option value="name_asc">Tên: A → Z</option>
                                    <option value="name_desc">Tên: Z → A</option>
                                </select>
                            </div>
                        </div>


                        <div class="page_amount">
                            {{-- <p>Showing 1–9 of 21 results</p> --}}
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
        </div>
    </div>



    {{-- --- Modal Attributes --- --}}
    <div class="modal fade" id="attributeModal" tabindex="-1" aria-labelledby="attributeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-md">
            <div class="modal-content shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title mb-0">
                        <i class="mdi mdi-filter-menu"></i> Lọc theo thuộc tính
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @php
                        $selectedAttrs = is_array($selectedAttrs) ? $selectedAttrs : explode(',', $selectedAttrs);
                    @endphp
                    @forelse ($attributes as $attr)
                        <div class="mb-4">
                            <h6 class="fw-semibold text-dark">{{ $attr->name }}</h6>
                            <div class="attribute-tags">
                                @foreach ($attr->values as $val)
                                    <input type="checkbox" class="btn-check attribute-filter" value="{{ $val->id }}"
                                        id="attrval-{{ $val->id }}"
                                        {{ in_array($val->id, $selectedAttrs) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-dark btn-sm rounded-pill me-1 mb-2"
                                        for="attrval-{{ $val->id }}">
                                        {{ $val->value }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted">Không có thuộc tính nào để lọc.</p>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="applyAttributeFilter">Áp dụng</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/cleave.min.js"></script>
<script>
$(function(){
    const MAX_PRICE = 100000000;

const cleaveMin = new Cleave('#min-price', {
    numeral: true,
    delimiter: '.',
    numeralDecimalMark: ',',
    numeralThousandsGroupStyle: 'thousand',
    numeralDecimalScale: 0,
    numeralPositiveOnly: true
});
const cleaveMax = new Cleave('#max-price', {
    numeral: true,
    delimiter: '.',
    numeralDecimalMark: ',',
    numeralThousandsGroupStyle: 'thousand',
    numeralDecimalScale: 0,
    numeralPositiveOnly: true
});

function enforceMax(cleaveInst){
    const raw = cleaveInst.getRawValue();
    const num = parseInt(raw,10) || 0;
    if(num > MAX_PRICE){
        $('#price-error').text(`Giá tối đa là ${MAX_PRICE.toLocaleString()}`);
    } else {
        $('#price-error').text('');
    }
}

$('#min-price').on('input', function(){
    enforceMax(cleaveMin);
    fetchFilteredProducts();
});
$('#max-price').on('input', function(){
    enforceMax(cleaveMax);
    fetchFilteredProducts();
});

// Chặn nhập nếu đã đủ 9 chữ số raw, và chỉ cho phép [0-9.]
$('#min-price, #max-price')
    .on('keypress', function(e){
        // tìm instance tương ứng
        const inst = this.id==='min-price' ? cleaveMin : cleaveMax;
        // nếu là chữ số và raw đã đủ 9 thì block
        if(/\d/.test(e.key) && inst.getRawValue().length >= 9){
            e.preventDefault();
            return;
        }
        // block ký tự không phải [0-9 .]
        if(!/[0-9\.]/.test(e.key)){
            e.preventDefault();
        }
    })
    .on('paste', function(e){
        const inst = this.id==='min-price' ? cleaveMin : cleaveMax;
        // lấy chỉ chữ số từ paste
        const pasted = (e.originalEvent || e).clipboardData.getData('text').replace(/\D/g,'');
        // nếu sẽ vượt quá 9 chữ số raw → block
        if(inst.getRawValue().length + pasted.length > 9){
            e.preventDefault();
        }
    });

    // 4) Chặn nhập ký tự không phải số / dấu '.'
    $('#min-price, #max-price')
        .on('keypress', function(e){
            if(!/[0-9\.]/.test(e.key)) e.preventDefault();
        })
        .on('paste', function(e){
            const text = (e.originalEvent || e).clipboardData.getData('text');
            if(!/^[0-9\.]+$/.test(text)) e.preventDefault();
        });

    // 5) Validate trước khi gọi AJAX (nếu cần)
    function validatePrices(){
        let min = parseInt(cleaveMin.getRawValue(), 10) || 0;
        let max = parseInt(cleaveMax.getRawValue(), 10) || 0;
        if(min < 0) {
            $('#price-error').text('Giá thấp ≥ 0');
            return false;
        }
        if(max < 0) {
            $('#price-error').text('Giá cao ≥ 0');
            return false;
        }
        if(max > MAX_PRICE){
            $('#price-error').text(`Giá cao ≤ ${MAX_PRICE.toLocaleString()}`);
            return false;
        }
        if(min > max){
            $('#price-error').text('Giá thấp ≤ Giá cao');
            return false;
        }
        $('#price-error').text('');
        return true;
    }

    // 6) Hàm lấy và render sản phẩm
    function fetchFilteredProducts(){
        if(!validatePrices()) return;
        let params = new URLSearchParams();

        // Categories
        $('.category-filter:checked').each((_,el) => {
            params.append('categories[]', el.value);
        });
        // Brands
        $('.brand-filter:checked').each((_,el) => {
            params.append('brands[]', el.value);
        });
        // Attributes
        $('.attribute-filter:checked').each((_,el) => {
            params.append('attribute_values[]', el.value);
        });
        // Price
        const min = cleaveMin.getRawValue();
        const max = cleaveMax.getRawValue();
        if(min) params.append('min_price', min);
        if(max) params.append('max_price', max);
        // Sort
        const order = $('#orderby').val();
        if(order) params.append('orderby', order);

        fetch(`{{ route('client.filter') }}?${params}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(json => {
            $('#product-list').html(json.products);
            $('#pagination').html(json.pagination);
        });
    }

    // 7) Khởi động sự kiện khác
    $('.category-filter, .brand-filter').on('change', fetchFilteredProducts);
    $('#orderby').on('change', fetchFilteredProducts);
    $('#applyAttributeFilter').on('click', () => {
        bootstrap.Modal.getInstance($('#attributeModal')).hide();
        fetchFilteredProducts();
    });
});
</script>
@endsection

