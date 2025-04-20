@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh sách sản phẩm</h4>
                    <a href="{{ route('admin.products.create') }}" type="button" class="btn btn-sm btn-primary">Thêm sản phẩm</a>

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
                                    class="btn btn-primary">Lấy tệp file</a>
                            </div>



                        </div>

                    </form>


                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>

                                <th>Tên sản phẩm</th>
                                <th>Ảnh</th>
                                <th>Thương hiệu</th>
                                <th>Danh mục</th>
                                <th>Số lượng</th>

                                <th>Giá</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($products as $key => $product)
                            <tr>

                                <td>{{ $product->name }}</td>
                                <td><img src="{{asset($product->image_primary) }}" alt="err" height="60px"></td>
                                <td>{{ $product->brand->name }}</td>
                                <td>{{$product->category->name }}</td>
                                <td>{{ $product->total_quantity }}</td>

                                <td>{{ $product->min_price }}-{{ $product->max_price }}</td>

                                <td>
                                    <a href="{{ route('admin.products.edit',$product->id) }}">
                                        <button type="button" class="btn btn-secondary btn-warning">Sửa</button>
                                    </a>
                                    <button type="submit"
                                        class="btn btn-secondary btn-danger delete-product">Khóa</button>
                                    <a href="{{ route('admin.products.detail',$product->id) }}"><button
                                            class="btn btn-info">Chi tiết</button></a>
                                    <a href="{{ route('admin.products.stas',$product->id) }}"><button
                                            class="btn btn-primary">Thống kê</button></a>
                                </td>
                            </tr>
                            @endforeach


                        </tbody>
                        <tfoot>
                            <tr>

                                <th>Tên sản phẩm</th>
                                <th>Ảnh</th>
                                <th>Thương hiệu</th>
                                <th>Danh mục</th>
                                <th>Số lượng</th>

                                <th>Giá</th>
                                <th>Hành động</th>
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

@push('scripts')
<x-admin.data-table-scripts />
@endpush