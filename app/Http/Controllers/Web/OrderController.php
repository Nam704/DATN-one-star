<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Lấy tham số status từ URL, nếu không có mặc định là "all"
        $status = $request->get('status', 'all');

        // Lấy đơn hàng kèm quan hệ (orderStatus và orderDetails)
        $query = Order::with('orderStatus', 'orderDetails');

        if ($status != 'all') {
            // Lọc theo tên trạng thái (ví dụ: pending, processing, shipped, completed, cancelled)
            $query->whereHas('orderStatus', function ($q) use ($status) {
                $q->where('name', $status);
            });
        }
        $orders = $query->orderBy('created_at', 'desc')->get();

        return view('client.orders.index', compact('orders', 'status'));
    }

    // Phương thức hiển thị chi tiết đơn hàng (nếu cần)
    public function getOrderDetails($id)
    {
        $order = Order::with('orderDetails.productVariant')->find($id);

        if (!$order) {
            return response('Order not found.', 404);
        }

        return view('client.orders.partials.order_details', compact('order'));
    }
    public function cancel($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Kiểm tra nếu đơn hàng đang ở trạng thái 'pending'
        if ($order->orderStatus->name === 'pending') {
            // Cập nhật trạng thái thành 'cancelled'
            $order->id_order_status = Order_status::where('name', 'cancelled')->first()->id;
            $order->save();

            return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
        }

        return redirect()->back()->with('error', 'Đơn hàng này không thể hủy.');
    }

    public function reorder($orderId)
    {
        $order = Order::findOrFail($orderId);

        // Nếu đơn hàng đang ở trạng thái 'cancelled'
        if ($order->orderStatus->name === 'cancelled') {
            // Cập nhật trạng thái đơn hàng trở lại 'pending'
            $order->id_order_status = Order_status::where('name', 'pending')->first()->id;
            $order->save();

            return redirect()->back()->with('success', 'Đơn hàng đã được kích hoạt lại thành công.');
        }

        return redirect()->back()->with('error', 'Không thể mua lại đơn hàng này.');
    }




}
