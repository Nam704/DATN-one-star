@if ($orders->isEmpty())
    <tr>
        <td colspan="9" class="text-center">
            Không có đơn hàng nào phù hợp với bộ lọc. Vui lòng thử thay đổi tiêu chí tìm kiếm.
        </td>
    </tr>
@else
    @foreach ($orders as $order)
        <tr data-id="{{ $order->id }}">
            <td>{{ $order->code }}</td>
            <td>{{ json_decode($order->user_data, true)['name'] ?? 'N/A' }}</td>
            <td>{{ json_decode($order->user_data, true)['email'] ?? 'N/A' }}</td>
            <td>
                <span
                    class="badge {{ $order->orderStatus->group_status == 'Delivered' ? 'bg-success' : ($order->orderStatus->group_status == 'Cancelled' ? 'bg-danger' : 'bg-warning') }}">
                    {{ $order->orderStatus->name ?? 'N/A' }}
                </span>
            </td>
            <td>{{ number_format($order->total) }} </td>
            {{-- <td>{{ number_format($order->shipping) }}</td> --}}
            <td>{{ $order->payment_method }}</td>
            <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
            <td>
                <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn btn-sm btn-info">Xem</a>
                @if (in_array($order->orderStatus->name, ['Cancel Requested', 'Cancel Under Review']))
                    <form action="{{ route('admin.orders.process_cancellation', $order->id) }}" method="POST"
                        class="cancel-order-form" style="display:inline;">
                        @csrf
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="btn btn-sm btn-success">Chấp nhận</button>
                    </form>
                @elseif (
                    !in_array($order->orderStatus->name, [
                        'Shipping',
                        'Delivered',
                        'Cancelled',
                        'Refunded',
                        'Return Rejected',
                        'Payment Retry Requested',
                        'Payment Expired',
                        'Payment Failed',
                        'Paid',
                    ]) && !in_array($order->orderStatus->group_status, ['Cancelled']))
                    <button class="btn btn-sm btn-warning update-status" data-id="{{ $order->id }}">Cập nhật
                    </button>
                @endif
            </td>
        </tr>
    @endforeach
@endif
