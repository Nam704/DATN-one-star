@extends('admin.layouts.layout')
@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Đơn hàng của: <strong>{{ $userName }}</strong></h4>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm">
      <i class="mdi mdi-arrow-left"></i> Quay lại Trang quản trị
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0">
          <thead class="thead-light">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Mã đơn</th>
              <th scope="col">Ngày tạo</th>
              <th scope="col" class="text-end">Tổng tiền</th>
              <th scope="col">Trạng thái</th>
              <th scope="col">Hành động</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $i => $order)
              <tr>
                <th scope="row">{{ $i + 1 }}</th>
                <td>{{ $order->code }}</td>
                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                <td class="text-end">
                  {{ number_format($order->total, 0, ',', '.') }} đ
                </td>
                <td>
                  <span class="badge
                    @if($order->orderStatus->name === 'Pending') badge-warning
                    @elseif($order->orderStatus->name === 'Completed') badge-success
                    @elseif($order->orderStatus->name === 'Cancelled') badge-danger
                    @else badge-secondary
                    @endif
                  ">
                    {{ $order->orderStatus->name ?? '-' }}
                  </span>
                </td>
                <td>
                  <a href="{{ route('admin.orders.detail', $order->id) }}"
                     class="btn btn-sm btn-primary" title="Xem chi tiết">
                    <i class="mdi mdi-eye-outline"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  <i class="mdi mdi-information-outline me-2"></i>Chưa có đơn hàng
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
