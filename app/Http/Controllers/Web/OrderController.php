<?php

namespace App\Http\Controllers\Web;

use App\Events\OrderNotification;
use App\Http\Controllers\Controller;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function detail($id)
    {
        $order = $this->orderService->getOrderDetail($id);

        return view('admin.order.detail', compact('order'));
    }
    public function list(Request $request)
    {
        $statuses = $this->orderService->listStatus();

        $orders = $this->orderService->getOrdersByStatus(1);
        return view('admin.order.list', compact('orders', 'statuses'));
    }
    public function update(Request $request)
    {
        $status = $request->input('status');
        $statuses = $this->orderService->listStatus();
        $checked = $status;
        $orders = $this->orderService->getOrdersByStatus($status);

        $html = view('admin.order.order_list', compact('orders', 'statuses', 'checked'))->render();

        return response()->json([
            'html' => $html,
            'status' => $status
        ]);
    }
    public function updateStatus($orderId)
    {
        // Gọi phương thức updateOrderStatus trong service
        $order = $this->orderService->updateOrderStatus($orderId);

        if (!$order) {
            return response()->json(['error' => 'Trang thái đạt tối đa'], 400);
        }
        event(new OrderNotification($order));
        return response()->json([
            'message' => 'Cập nhật trạng thái thành công.',
            'order' => $order,

        ]);
    }
}
