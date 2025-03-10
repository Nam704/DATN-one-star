<?php

namespace App\Http\Controllers\Web;

use App\Events\OrderUpdate;
use App\Events\OrderUpdated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_status;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'user',
            'address',
            'orderStatus',
            'voucher',
            'orderDetails.productVariant.product'
        ])->get();
        $orderStatuses = Order_status::all();
        return view('admin.orders.index', compact('orders', 'orderStatuses'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'order_status_id' => 'required|exists:order_statuses,id',
        ]);

        $order = Order::with(['user', 'address', 'orderStatus', 'voucher'])->findOrFail($request->order_id);
        $order->id_order_status = $request->order_status_id;
        $order->save();

        // Tải lại các quan hệ để đảm bảo dữ liệu mới nhất
        $order->load(['user', 'address', 'orderStatus', 'voucher']);

        // Phát sóng sự kiện
        broadcast(new OrderUpdated($order))->toOthers();;

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đơn hàng đã được cập nhật thành công!',
            'order' => $order
        ]);
    }
}
