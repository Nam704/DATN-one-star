@extends('admin.layouts.layout')
@section('content')
{{-- <h2>{{ $title }}</h2> --}}
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh sách nhà cung cấp</h4>
                    @if (session('success'))
                    <p class="alert alert-primary">
                        {{ session('success') }}
                    </p>
                    @endif

                    <a href="{{route('admin.suppliers.getFormAdd') }}" type="button" class="btn btn-sm btn-primary">
                        Thêm mới
                    </a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                            <tr>
                                <th>Tên</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>

                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($suppliers as $supplier)
                            <tr>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->full_address }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->status }}</td>
                                <td>
                                    <a type="button" class="btn btn-danger rounded-pill"
                                        href="{{ route('admin.suppliers.lockOrActive',$supplier->id) }}">
                                        @if ($supplier->status=='active')
                                        {{ 'Lock' }}
                                        @else

                                        {{ 'Active' }}
                                        @endif
                                    </a>
                                    <a type="button" class="btn btn-warning rounded-pill"
                                        href="{{ route('admin.suppliers.getFormUpdate',$supplier->id)}}">Edit</a>
                                </td>

                            </tr>
                            @endforeach


                        </tbody>
                        <tfoot>
                        <tr>
                                <th>Tên</th>
                                <th>Địa chỉ</th>
                                <th>Số điện thoại</th>
                                <th>Trạng thái</th>
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