<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function trackOrders()
    {
        // Lấy đơn hàng của người dùng hiện tại kèm quan hệ trạng thái, chi tiết và voucher (nếu có)
        $orders = Order::with([
            'orderStatus',
            'orderDetails.productVariant.images', // nạp luôn productVariant và hình ảnh của nó
            'voucher'
        ])->where('id_user', auth()->id())->get();

        return view('client.orders.track', compact('orders'));
    }


    public function cancelOrder(Order $order)
    {
        // Kiểm tra quyền: đơn hàng phải thuộc về người dùng hiện tại
        if ($order->id_user !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Chỉ cho phép hủy nếu đơn hàng đang ở trạng thái "Pending"
        if ($order->orderStatus->name !== 'pending') {
            return redirect()->back()->with('error', 'Chỉ đơn hàng đang ở trạng thái "Pending" mới có thể hủy.');
        }

        // Lấy trạng thái "Cancelled"
        $cancelledStatus = Order_status::where('name', 'Cancelled')->first();
        if (!$cancelledStatus) {
            return redirect()->back()->with('error', 'Trạng thái "Cancelled" không tồn tại.');
        }

        // Cập nhật trạng thái đơn hàng thành "Cancelled"
        $order->id_order_status = $cancelledStatus->id;
        $order->save();

        return redirect()->back()->with('success', 'Đơn hàng đã được hủy thành công.');
    }



    public function reorder(Order $order)
{
    // Kiểm tra quyền: đơn hàng phải thuộc về người dùng hiện tại
    if ($order->id_user !== Auth::id()) {
        return redirect()->back()->with('error', 'Bạn không có quyền mua lại đơn hàng này.');
    }

    // Chỉ cho phép mua lại nếu đơn hàng đã ở trạng thái "Cancelled"
    if ($order->orderStatus->name !== 'cancelled') {
        return redirect()->back()->with('error', 'Chỉ đơn hàng đã hủy mới có thể mua lại.');
    }

    // Lấy trạng thái "Pending" để khôi phục đơn hàng
    $pendingStatus = Order_status::where('name', 'Pending')->first();
    if (!$pendingStatus) {
        return redirect()->back()->with('error', 'Trạng thái "Pending" không tồn tại.');
    }

    // Cập nhật lại trạng thái đơn hàng thành "Pending"
    $order->id_order_status = $pendingStatus->id;
    $order->save();

    return redirect()->back()->with('success', 'Đơn hàng đã được khôi phục thành công.');
}


}
