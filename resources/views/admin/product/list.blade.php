@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">List Product</h4>
                        <a href="{{ route('admin.products.create') }}" type="button" class="btn btn-sm btn-primary">Add new
                            product</a>

                        <form class="form-control mt-2" action="{{ route('admin.excels.createProduct') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row container">
                                <div class="col-3">
                                    <label>Chọn file Excel:</label>
                                    <input type="file" class=" form-control" name="excel_file" required>
                                </div>
                                {{-- <div class="col-3">
                                <label>Chọn ảnh sản phẩm:</label>
                                <input type="file" class="form-control" name="product_images[]" multiple required>
                            </div> --}}
                                <div class="col-3 align-content-end">

                                    <button type="submit" class="btn btn-info">Nhập sản phẩm</button>
                                </div>
                                <div class="col-3 align-content-end">
                                    <a href="{{ route('admin.products.exportCreateExcel') }}" type="button"
                                        class="btn btn-primary">Get Sample file</a>
                                </div>
                            </div>
                        </form>

                        <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                            data-bs-target="#filterModal">
                            <i class="mdi mdi-filter-menu fs-5"></i> Filter Menu
                        </button>
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
                <form id="filterForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Products</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Category -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Brand -->
                        <div class="mb-3">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand" name="brand">
                                <option value="">All Brands</option>
                                @foreach ($brands as $b)
                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Stock -->
                        <div class="mb-3">
                            <label for="stock" class="form-label">Kho hàng</label>
                            <select class="form-select" id="stock" name="stock">
                                <option value="">Tất cả</option>
                                <option value="in_stock">Còn hàng</option>
                                <option value="out_of_stock">Hết hàng</option>
                                <option value="low_stock">Sắp hết hàng</option>  {{-- mới --}}
                                <option value="quantity">Số lượng cụ thể</option>
                            </select>
                            <input type="number" class="form-control mt-2" id="quantity" name="quantity"
                                placeholder="Nhập số lượng" style="display: none;">
                        </div>
                        <!-- Price -->
                        <div class="mb-3">
                            <label class="form-label">Khoảng giá</label>
                            <div class="row">
                                <div class="col">
                                    <input type="number" class="form-control" name="min_price" placeholder="Giá tối thiểu">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" name="max_price" placeholder="Giá tối đa">
                                </div>
                            </div>
                        </div>
                        <!-- Created At -->
                        <div class="mb-3">
                            <label class="form-label">Ngày tạo</label>
                            <div class="row">
                                <div class="col">
                                    <input type="date" class="form-control" name="created_from" placeholder="Từ ngày">
                                </div>
                                <div class="col">
                                    <input type="date" class="form-control" name="created_to" placeholder="Đến ngày">
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                Chỉ cần chọn một trong hai hoặc cả hai để lọc.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
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
                    $('#quantity').hide().val(''); // Ẩn và xóa giá trị khi không cần
                }
            });

            // Bắt sự kiện click nút Apply Filter
            $('#applyFilter').on('click', function(e) {
                e.preventDefault();
                var formData = $('#filterForm').serialize();
                console.log('Form data:', formData); // Debug dữ liệu gửi đi

                $.ajax({
                    url: '{{ route('admin.products.filter') }}',
                    method: 'GET',
                    data: formData,
                    success: function(html) {
                        $('#fixed-header-datatable tbody').html(html);
                        $('#filterModal').modal('hide');
                    },
                    error: function(xhr) {
                        console.error('AJAX error:', xhr.responseText);
                    }
                });
            });
        });
    </script>
@endpush
