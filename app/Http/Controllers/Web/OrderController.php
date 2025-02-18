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


}
