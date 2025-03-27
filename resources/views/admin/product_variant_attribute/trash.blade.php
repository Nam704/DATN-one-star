@extends('admin.layouts.layout')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="page-title-box">
                                    <h4 class="page-title">Danh sách giá trị thuộc tính biến thể đã xóa</h4>
                                    <a href="{{ route('admin.product_variant_attributes.index') }}"
                                        style="margin-bottom: 20px" class="btn btn-warning">
                                        Quay lại danh sách
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if (session('message'))
                            <div class="alert alert-primary" role="alert">
                                {{ session('message') }}
                            </div>
                        @endif
                        @if (session('message_error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('message_error') }}
                            </div>
                        @endif
                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sản phẩm</th>
                                    <th>Mã biến thể</th>
                                    <th>Giá trị thuộc tính</th>
                                    <th>Ngày xóa</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($trash_pva as $key => $value)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $value->product->name }}</td>
                                        <td>{{ $value->productVariant->sku }}</td>
                                        <td>{{ $value->attributeValue->value }}</td>
                                        <td>{{ \Carbon\Carbon::parse($value->deleted_at)->format('d/m/Y H:i:s') }}</td>
                                        <td>
                                            <form action="{{ route('admin.product_variant_attributes.restore', $value->id) }}"
                                                method="POST">
                                                @csrf
                                                <button type="submit"
                                                    onclick="return confirm('Bạn có muốn khôi phục không?')"
                                                    class="btn btn-secondary m-1"><i
                                                        class="bi-arrow-return-left me-2"></i>Khôi phục</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <th>STT</th>
                                <th>Sản phẩm</th>
                                <th>Mã biến thể</th>
                                <th>Giá trị thuộc tính</th>
                                <th>Ngày xóa</th>
                                <th>Hành động</th>
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
