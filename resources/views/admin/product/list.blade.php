@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div
                            class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between">
                            <h4 class="card-title mb-2 mb-md-0">List Products</h4>

                            <div class="d-flex flex-wrap align-items-center gap-2">

                                <!-- Add New Product -->
                                <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">
                                    <i class="mdi mdi-plus"></i> Add Product
                                </a>

                                <!-- Import/Export Group -->
                                <div class="dropdown">
                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" id="excelDropdown"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="mdi mdi-file-excel"></i> Excel
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-3" aria-labelledby="excelDropdown"
                                        style="min-width: 250px;">
                                        <!-- Download sample -->
                                        <li class="mb-2">
                                            <a href="{{ route('admin.products.exportCreateExcel') }}"
                                                class="btn btn-outline-secondary w-100 btn-sm">
                                                <i class="mdi mdi-download"></i> Download Sample
                                            </a>
                                        </li>
                                        <!-- Import form -->
                                        <li>
                                            <form action="{{ route('admin.products.importProduct') }}" method="POST"
                                                enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                                @csrf
                                                <label for="excel_file" class="form-label mb-0">Upload Excel</label>
                                                <input type="file" name="excel_file" id="excel_file"
                                                    class="form-control form-control-sm" accept=".xlsx,.xls" required>
                                                <button type="submit" class="btn btn-success btn-sm mt-1">
                                                    <i class="mdi mdi-upload"></i> Import
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Filter Modal Trigger -->
                                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#filterModal">
                                    <i class="mdi mdi-filter-menu"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="card-body">

                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <thead>
                                <tr>

                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Views</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @include('admin.product.product_table', ['products' => $products])


                            </tbody>
                            <tfoot>
                                <tr>

                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Brand</th>
                                    <th>Category</th>
                                    <th>Quantity</th>
                                    <th>Views</th>
                                    <th>Price</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div> <!-- end card body-->
                </div> <!-- end card -->
            </div><!-- end col-->
        </div> <!-- end row-->
    </div>



    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="filterForm" action="{{ route('admin.products.filter') }}" method="GET">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Products</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <!-- Category -->
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Brand -->
                            <div class="col-md-6">
                                <label for="brand" class="form-label">Brand</label>
                                <select class="form-select" id="brand" name="brand">
                                    <option value="">All Brands</option>
                                    @foreach ($brands as $b)
                                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Stock -->
                        <div class="mb-3">
                            <label for="stock" class="form-label">Kho hàng</label>
                            <select class="form-select" id="stock" name="stock">
                                <option value="">Tất cả</option>
                                <option value="in_stock">Còn hàng</option>
                                <option value="out_of_stock">Hết hàng</option>
                                <option value="low_stock">Sắp hết hàng</option> {{-- mới --}}
                                <option value="quantity">Số lượng cụ thể</option>
                            </select>
                            <input type="number" class="form-control mt-2" id="quantity" name="quantity"
                                placeholder="Nhập số lượng" style="display: none;">
                        </div>
                        <!-- Sort View -->
                        <div class="mb-3">
                            <label for="sort_view" class="form-label">Sắp xếp theo lượt xem</label>
                            <select class="form-select" id="sort_view" name="sort_view">
                                <option value="">Không sắp xếp</option>
                                <option value="asc">Từ thấp đến cao</option>
                                <option value="desc">Từ cao đến thấp</option>
                            </select>
                        </div>
                        <!-- Price -->
                        <div class="mb-3">
                            <label class="form-label">Khoảng giá</label>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" name="min_price"
                                        placeholder="Giá tối thiểu" maxlength="20" inputmode="decimal">
                                </div>
                                <div class="col">
                                    <input type="text" class="form-control" name="max_price" placeholder="Giá tối đa"
                                        maxlength="20" inputmode="decimal">
                                </div>
                            </div>
                        </div>
                        <!-- Created At -->
                        <div class="mb-3">
                            <label class="form-label">Ngày tạo</label>
                            <div class="row">
                                <div class="col">
                                    <input type="date" class="form-control" name="created_from"
                                        value="{{ old('created_from') }}" max="{{ now()->toDateString() }}"
                                        onchange="this.blur()" placeholder="Từ ngày">
                                </div>
                                <div class="col">
                                    <input type="date" name="created_to" id="created_to" class="form-control"
                                        value="{{ old('created_to') }}" max="{{ now()->toDateString() }}"
                                        onchange="this.blur()">
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Chỉ cần chọn một trong hai hoặc cả hai để lọc.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="button" class="btn btn-outline-warning" id="resetFilter">Reset bộ lọc</button>
                        <button type="button" class="btn btn-primary" id="applyFilter">Áp dụng bộ lọc</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush
@push('scripts')
    <script>
        $(document).ready(function() {
            // Hiển thị/ẩn input quantity
            $('#stock').on('change', function() {
                if (this.value === 'quantity') {
                    $('#quantity').show();
                } else {
                    $('#quantity').hide().val('');
                }
            });

            // Xóa hết các error trước khi chạy validate/lần AJAX mới
            function clearErrors() {
                $('.validation-error').remove();
                $('.is-invalid').removeClass('is-invalid');
                $('#filterModal .modal-body .alert').remove();
                // $('#price-error').hide().text('');
                // $('#date-error').addClass('d-none').find('.date-error-text').text('');
            }

            $('#applyFilter').on('click', function(e) {
                e.preventDefault();
                clearErrors();

                // Lấy nguyên raw input (có dấu . hay - nếu user gõ)
                var minRaw = $('input[name="min_price"]').val().trim();
                var maxRaw = $('input[name="max_price"]').val().trim();
                var errors = {}; // <-- Đưa lên đầu

var fromDate = $('[name="created_from"]').val();
var toDate = $('[name="created_to"]').val();

if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
    errors.created_to = 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.';
}


                // 1. Nếu user nhập giá, phải chỉ gồm [0-9 .] thôi
                var validPattern = /^[0-9.]+$/;
                if (minRaw && !validPattern.test(minRaw)) {
                    errors.min_price = 'Giá tối thiểu chỉ được gồm chữ số và dấu chấm.';
                }
                if (maxRaw && !validPattern.test(maxRaw)) {
                    errors.max_price = 'Giá tối đa chỉ được gồm chữ số và dấu chấm.';
                }

                // 2. Chuyển về số thực để so sánh
                if (minRaw && validPattern.test(minRaw)) {
                    var minVal = parseInt(minRaw.replace(/\./g, ''), 10);
                    if (isNaN(minVal) || minVal < 0) {
                        errors.min_price = 'Giá tối thiểu phải lớn hơn hoặc bằng 0.';
                    } else if (minVal > 100000000) {
                        errors.min_price = 'Giá tối thiểu không được vượt quá 100.000.000.';
                    }
                }
                if (maxRaw && validPattern.test(maxRaw)) {
                    var maxVal = parseInt(maxRaw.replace(/\./g, ''), 10);
                    if (isNaN(maxVal) || maxVal < 0) {
                        errors.max_price = 'Giá tối đa phải lớn hơn hoặc bằng 0.';
                    } else if (maxVal > 100000000) {
                        errors.max_price = 'Giá tối đa không được vượt quá 100.000.000.';
                    }
                }

                // 3. So sánh min <= max
                if (!errors.min_price && !errors.max_price && minRaw && maxRaw) {
                    var minVal = parseInt(minRaw.replace(/\./g, ''), 10);
                    var maxVal = parseInt(maxRaw.replace(/\./g, ''), 10);
                    if (maxVal < minVal) {
                        errors.max_price = 'Giá tối đa phải lớn hơn hoặc bằng giá tối thiểu.';
                    }
                }

                // Nếu có lỗi thì hiển thị và dừng
                if (Object.keys(errors).length) {
                    $('#filterModal .modal-body')
                        .prepend(
                            '<div class="alert alert-danger validation-error">Vui lòng sửa trước khi lọc:</div>'
                        );
                    $.each(errors, function(field, msg) {
                        var $fld = $('[name="' + field + '"]');
                        $fld.addClass('is-invalid');
                        $('<small class="text-danger validation-error">' + msg + '</small>')
                            .insertAfter($fld);
                    });
                    return;
                }
                // ngược lại gửi AJAX như cũ
                $.ajax({
                    url: $('#filterForm').attr('action'),
                    method: $('#filterForm').attr('method'),
                    data: $('#filterForm').serialize(),
                    success(html) {
                        $('#fixed-header-datatable tbody').html(html);
                        $('#filterModal').modal('hide');
                    },
                    error(xhr) {
                        console.error(xhr);
                        alert('Có lỗi, thử lại.');
                    }
                });
            });

            $('#resetFilter').on('click', function(e) {
                e.preventDefault();
                clearErrors();
                $('#filterForm')[0].reset();
                $('#quantity').hide();
            });
            $('#filterModal input[name="min_price"], #filterModal input[name="max_price"]')
                .attr('inputmode', 'numeric')
                .on('input', function() {
                    // 1) Loại bỏ hết ký tự không phải số
                    let s = this.value.replace(/\D/g, '');

                    // 2) Giới hạn độ dài tối đa 9 ký tự (max 999999999)
                    if (s.length > 9) {
                        s = s.slice(0, 9);
                    }

                    // 3) Gán lại raw digits (không clamp giá trị)
                    this.value = s;
                });

        });
    </script>
@endpush
