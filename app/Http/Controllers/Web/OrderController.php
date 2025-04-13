<?php

namespace App\Http\Controllers\Web;

use App\Events\OrderNotification;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderServiceManager as OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $orderService;
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }
    public function processCancellation(Request $request, $orderId)
    {
        try {
            // Validate input
            $request->validate([
                'action' => 'required|in:review,approve',
                'admin_note' => 'nullable|string|max:500',
            ], [
                'action.required' => 'Vui lòng chọn hành động.',
                'action.in' => 'Hành động không hợp lệ.',

            ]);
            $action = $request->input('action');
            // Gọi processCancelRequest từ OrderServiceManager
            $order = $this->orderService->processCancelRequest($orderId, $action);
            // Thông báo thành công
            $message = match ($action) {
                'review' => 'Yêu cầu hủy đơn hàng đã được chuyển sang trạng thái xem xét.',
                'approve' => 'Yêu cầu hủy đơn hàng đã được phê duyệt.',
                default => 'Xử lý yêu cầu hủy thành công.',
            };

            return redirect()->back()->with('success', $message);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Lỗi khi xử lý yêu cầu hủy: ' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
    public function list(Request $request)
    {
        $data = $this->orderService->searchOrders($request, 10);

        if (isset($data['errors'])) {
            if ($request->ajax()) {
                return response()->json($data, 422);
            } else {
                return back()->withErrors($data['errors'])->withInput();
            }
        } else {
            if ($request->ajax()) {
                $html = view('admin.order.order_list', $data)->render();
                $pagination = $data['orders']->links()->render(); // Trả về HTML phân trang
                return response()->json([
                    'html' => $html,
                    'pagination' => $pagination,
                    'success' => true
                ]);
            } else {
                return view('admin.order.list', $data);
            }
        }
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
    public function detail($id)
    {
        $order = Order::with(['orderStatus', 'orderCancellations.reason'])->findOrFail($id);
        $orderDetails = $order->detailsOrder();
        $orderService = app(\App\Services\OrderService::class);

        // Tự động cập nhật trạng thái sang Cancel Under Review nếu là Cancel Requested
        if ($order->orderStatus->name === 'Cancel Requested') {
            try {
                $this->orderService->processCancelRequest($id, 'review');
            } catch (\Exception $e) {
                Log::error('Lỗi khi tự động chuyển sang Cancel Under Review: ' . $e->getMessage());
            }
        }

        return view('admin.order.detail', compact('order', 'orderDetails', 'orderService'));
    }
}
