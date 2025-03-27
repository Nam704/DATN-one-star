@foreach ($orders as $order)
    <tr>
        <td><input type="checkbox" name="selected_order[]" value="{{ $order->id }}" class="import-checkbox"></td>
        <td>
            {{ $order->code }}
        </td>
        <td>
            {{ $order->created_at }}
        </td>
        <td>
            {{ $order->total }}
        </td>
        <td>
            {{ $order->payment_method }} : {{ $order->payment_status }}
        </td>
        <td>
            <a href="{{ route('admin.orders.detail', $order->id) }}" class="btn  btn-primary">Detail</a>
            <button data-order-id="{{ $order->id }}" class="btn btn-warning accept">Accept</button>
            <button class="btn btn-danger">Reject</button>
        </td>

    </tr>
@endforeach
