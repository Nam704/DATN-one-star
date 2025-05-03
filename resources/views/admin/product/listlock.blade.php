@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">Danh sách sản phẩm ngừng bán</h4>
                    </div>

                    <div class="card-body">

                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped  w-100">
                            <thead>
                                <tr>

                                    <th>Tên</th>
                                    <th>Ảnh</th>
                                    <th>Thương hiệu</th>
                                    <th>Danh mục</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($products as $key => $product)
                                    <tr>

                                        <td>{{ $product->name }}</td>
                                        <td><img src="{{ asset($product->image_primary) }}" alt="err" height="60px">
                                        </td>
                                        <td>{{ $product->brand->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>

                                            <form action="{{ route('admin.products.openProduct', $product->id) }}"
                                                class="d-inline" method="POST"
                                                onclick="return confirm('Bạn có muốn cho sản phẩm này hoạt động lại không?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success ">Khôi phục</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>

                                    <th>Tên</th>
                                    <th>Ảnh</th>
                                    <th>Thương hiệu</th>
                                    <th>Danh mục</th>
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
