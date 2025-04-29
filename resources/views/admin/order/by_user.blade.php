@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">
  <h4>Đơn hàng của: {{ $userName }}</h4>
  <div class="table-responsive">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>#</th><th>Mã đơn</th><th>Ngày tạo</th><th>Tổng tiền</th><th>Trạng thái</th><th>Hành động</th>
        </tr>
      </thead>
      <tbody>
        @forelse($orders as $i => $order)
          <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $order->code }}</td>
            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ number_format($order->total,0,',','.') }} đ</td>
            <td>{{ $order->orderStatus->name ?? '-' }}</td>
            <td>
              <a href="{{ route('admin.orders.detail', $order->id) }}"
                 class="btn btn-sm btn-primary">
                Xem chi tiết
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center">Chưa có đơn hàng</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
