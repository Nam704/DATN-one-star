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




@endsection
@push('styles')
    <x-admin.data-table-styles />
@endpush

