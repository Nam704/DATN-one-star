@extends('admin.layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">Voucher List</h4>
                    <a href="{{ route('admin.vouchers.addVoucher') }}" class="btn btn-sm btn-primary">
                        Add New Voucher
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="voucher-table" class="table table-striped dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Description</th>
                                    <th>Discount Amount</th>
                                    <th>Quantity</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Min Amount</th>
                                    <th>Max Discount</th>
                                    <th>Type</th>
                                    <th>User Limit</th>
                                    <th>Total Usage</th>
                                    <th>Status</th>
                                    <th>Applies To</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($vouchers as $key => $voucher)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $voucher->name }}</td>
                                    <td>{{ $voucher->code }}</td>
                                    <td>{{ $voucher->description }}</td>
                                    <td>{{ number_format($voucher->discount_amount, 2) }}</td>
                                    <td>{{ number_format($voucher->quantity) }}</td>
                                    <td>{{ $voucher->start_date }}</td>
                                    <td>{{ $voucher->end_date }}</td>
                                    <td>{{ number_format($voucher->min_amount, 2) }}</td>
                                    <td>{{ number_format($voucher->max_discount_amount, 2) }}</td>
                                    <td>{{ $voucher->type }}</td>
                                    <td>{{ $voucher->user_limit }}</td>
                                    <td>{{ $voucher->total_usage }}</td>
                                    <td>
                                    <span class="badge bg-success">{{$voucher->status}}</span>
                                    </td>
                                    <td>{{ $voucher->applies_to_names }}</td>
                                    <td>
                                        <a href="{{ route('admin.vouchers.editVoucher', $voucher->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('admin.vouchers.deleteVoucher', $voucher->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this voucher?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div> <!-- end table-responsive -->
                </div> <!-- end card body -->
            </div> <!-- end card -->
        </div><!-- end col -->
    </div> <!-- end row -->
</div>
@endsection

@push('styles')
<!-- DataTable CSS -->
<style>
    /* Set max height for vertical scrolling */
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')
<!-- DataTable JS -->
<script>
    $(document).ready(function() {
        $('#voucher-table').DataTable({
            scrollY: "400px", // Cuộn dọc 400px
            scrollX: true, // Cuộn ngang nếu bảng rộng hơn màn hình
            paging: true,
            searching: true,
            ordering: true,
            responsive: true
        });
    });
</script>
@endpush
