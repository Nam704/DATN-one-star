@extends('client.layouts.layout')
@section('content')
<div class="container">
    <h1>Theo dõi đơn hàng của bạn</h1>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($orders->isEmpty())
      <p>Không có đơn hàng nào.</p>
    @else
      @foreach($orders as $order)
        <div class="card mb-4">
          <div class="card-header">
            <strong>Đơn hàng #{{ $order->id }}</strong> | SĐT: {{ $order->phone_number }} | Tổng tiền: {{ number_format($order->total_amount, 2) }}<br>
            Trạng thái: {{ $order->orderStatus->name }}
          </div>
          <div class="card-body">
            <h5>Sản phẩm:</h5>
            @php
              // Vì mỗi đơn hàng chỉ có 1 order_detail nên ta lấy phần tử đầu tiên
              $detail = $order->orderDetails->first();
            @endphp
            <p>
              Tên sản phẩm: {{ $detail->product_name }}<br>
              Đơn giá: {{ number_format($detail->unit_price, 2) }}<br>
              Số lượng: {{ $detail->quantity }}<br>
              Thành tiền: {{ number_format($detail->total_price, 2) }}
            </p>
            <div>
              @if($detail->productVariant && $detail->productVariant->images->isNotEmpty())
                <img src="{{ $detail->productVariant->images->first()->url }}" alt="{{ $detail->product_name }}" width="100">
              @else
                <p>Không có hình ảnh</p>
              @endif
            </div>

            @if($order->orderStatus->name === 'pending')
              <form action="{{ route('client.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                @csrf
                <button type="submit" class="btn btn-danger">Hủy đơn hàng</button>
              </form>
            @elseif($order->orderStatus->name === 'cancelled')
              <form action="{{ route('client.orders.reorder', $order->id) }}" method="POST" onsubmit="return confirm('Bạn có muốn mua lại đơn hàng này?');">
                @csrf
                <button type="submit" class="btn btn-primary">Mua lại</button>
              </form>
            @else
              <p>Đơn hàng này không thể hủy.</p>
            @endif
          </div>
        </div>
      @endforeach
    @endif
</div>
@endsection
