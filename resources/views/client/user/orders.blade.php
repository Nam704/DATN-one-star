<div class="tab-pane fade show active" id="orders">
    <h3>Orders</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="order-list">
                @foreach ($orders as $order)
                    <tr id="order-{{ $order->id }}">
                        <td>{{ $order->code }}</td>
                        <td>{{ $order->created_at }}</td>
                        <td><span class="status"
                                data-order-id="{{ $order->id }}">{{ $order->orderStatus->name }}</span></td>
                        <td>{{ $order->total }} </td>
                        <td><a href="{{ route('client.orders.detail', $order->id) }}" class="view">view</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
