@extends('admin.layouts.layout')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="mt-4">

                        <h1 class="text-center">Product Audit</h1>

                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                    </div>


                    <div class="card-body">
                        <div class="mb-3">
                            <a href="{{ route('admin.product_audits.create') }}" class="btn btn-primary">Thêm mới</a>
                        </div>


                        <table id="fixed-header-datatable"
                            class="table table-striped dt-responsive nowrap table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Người dùng</th>
                                    <th>Biến thể sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Loại hành động</th>
                                    <th>Lý do</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>

                            @foreach ($audits as $audit)
                                <tr>
                                    <td>{{ $audit->id }}</td>
                                    <td>{{ $audit->user->name }}</td>
                                    <td>{{ $audit->productVariant->sku }}</td>
                                    <td>{{ $audit->quantity }}</td>
                                    <td>{{ $audit->action_type }}</td>
                                    <td>{{ $audit->reason }}</td>
                                    <td>
                                        <a href="{{ route('admin.product_audits.edit', $audit->id) }}" class="btn btn-warning btn-sm">Chỉnh sửa</a>
                                        <a href="{{ route('admin.product_audits.show', $audit->id) }}" class="btn btn-primary btn-sm">Chi Tiết</a>
                                    </td>
                                </tr>
                            @endforeach
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
