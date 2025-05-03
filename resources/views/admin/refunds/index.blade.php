@extends('admin.layouts.layout')
@section('content')
    <div class="container">
        <h2>Danh Sách Yêu Cầu Hoàn Tiền</h2>

        <table class="table">
            <thead>
                <tr>
                    <th>Mã Yêu Cầu</th>
                    <th>Đơn Hàng</th>
                    <th>Người Yêu Cầu</th>
                    <th>Ngân Hàng</th>
                    <th>Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($refunds as $refund)
                    <tr>
                        <td>{{ $refund->id }}</td>
                        <td>Đơn #{{ $refund->order_id }}</td>
                        <td>{{ $refund->user->name }}</td>
                        <td>
                            {{ $refund->bank_details['bank_name'] }}<br>
                            {{ $refund->bank_details['account_number'] }}
                        </td>
                        <td>
                            <form action="{{ route('staff.refunds.approve', $refund) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Duyệt</button>
                            </form>

                            <form action="{{ route('staff.refunds.reject', $refund) }}" method="POST" class="mt-2">
                                @csrf
                                <input type="text" name="notes" placeholder="Lý do từ chối" required>
                                <button type="submit" class="btn btn-danger btn-sm">Từ Chối</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
