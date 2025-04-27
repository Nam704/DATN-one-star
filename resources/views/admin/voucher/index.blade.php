@extends('admin.layouts.layout')
@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Danh sách mã giảm giá</h4>


                    <a href="{{ route('admin.vouchers.addVoucher') }}" type="button" class="btn btn-sm btn-primary">
                        Thêm mới</a>

                </div>

                <div class="card-body">

                    <table id="fixed-header-datatable"
                        class="table table-striped dt-responsive nowrap table-striped  w-100">
                        <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên </th>
                                    <th>Mã </th>
                                    <th>Loại</th>
                                    <th>Giá trị giảm giá</th>
                                    <th>Số tiền tối thiểu</th>
                                    <th>Số tiền tối đa</th>
                                    <th>Trạng thái</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vouchers as $key => $voucher)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $voucher->name }}</td>
                                    <td>{{ $voucher->code }}</td>
                                    <td>{{ $voucher->type }}</td>
                                    <td>{{ number_format($voucher->discount_amount, 0) }}
                                    {{ $voucher->type == 'fixed' ? 'VNĐ' : '%' }}
                                    </td>
                                    <td>{{ number_format($voucher->min_amount, 0) }}VNĐ</td>
                                    <td>{{ number_format($voucher->max_discount_amount, 0) }}VNĐ</td>
                                    <td>
                                    <span class="badge bg-success">{{$voucher->status}}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.vouchers.editVoucher', $voucher->id) }}" class="btn btn-warning ">Sửa</a>
                                        <a href="{{ route('admin.vouchers.detailVoucher', $voucher->id) }}" class="btn btn-info ">Chi tiết</a>
                                        <form action="{{ route('admin.vouchers.deleteVoucher', $voucher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this voucher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Xóa</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        <tfoot>
                        <tr>
                                    <th>STT</th>
                                    <th>Tên </th>
                                    <th>Mã </th>
                                    <th>Loại</th>
                                    <th>Giá trị giảm giá</th>
                                    <th>Số tiền tối thiểu</th>
                                    <th>Số tiền tối đa</th>
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
