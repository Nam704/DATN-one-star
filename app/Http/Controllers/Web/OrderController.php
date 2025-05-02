<?php

namespace App\Http\Controllers\Web;

use App\Events\OrderNotification;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderServiceManager as OrderService;
use Carbon\Carbon;
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
    public function getRestrictedUsers(Request $request)
    {
        try {
            $result = $this->orderService->getRestrictedUsers($request);
            return response()->json([
                'success' => true,
                'data' => $result,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function unrestrictUser($userId)
    {
        try {
            $this->orderService->unrestrictUser($userId);
            return response()->json([
                'success' => true,
                'message' => 'Tài khoản đã được mở khóa.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    public function processCancellation(Request $request, $orderId)
    {

        try {

            $request->validate([
                'action' => 'required|in:review,approve,reject',
                'admin_note' => 'required_if:action,reject|string|nullable|max:500',
            ], [
                'action.required' => 'Vui lòng chọn hành động.',
                'action.in' => 'Hành động không hợp lệ.',
                'admin_note.required_if' => 'Ghi chú là bắt buộc khi từ chối hủy đơn.',
            ]);
            Log::info('Request data:', $request->all());
            $action = $request->input('action');
            $adminNote = $request->input('admin_note');

            $order = $this->orderService->processCancelRequest($orderId, $action, $adminNote);

            $message = match ($action) {
                'review' => 'Yêu cầu hủy đơn hàng đang được xem xét.',
                'approve' => 'Yêu cầu hủy đơn hàng đã được phê duyệt.',
                'reject' => 'Yêu cầu hủy đơn hàng đã bị từ chối.',
                default => 'Xử lý yêu cầu hủy thành công.',
            };

            return redirect()->back()->with('success', $message);
        } catch (ValidationException $e) {
            $firstError = $e->validator->errors()->first();
            return redirect()->back()
                ->with('error', $firstError)
                ->withInput();
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
                return response()->json([
                    'errors' => $data['errors'],
                    'message' => 'Dữ liệu đầu vào không hợp lệ. Vui lòng kiểm tra lại.'
                ], 422);
            } else {
                return back()->withErrors($data['errors'])->withInput();
            }
        } else {
            if ($request->ajax()) {
                $html = view('admin.order.order_list', $data)->render();
                $pagination = $data['orders']->links()->render();
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
        try {
            $order = $this->orderService->updateOrderStatus($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trạng thái đã đạt tối đa hoặc không thể cập nhật.',
                ], 400);
            }

            event(new OrderNotification($order));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật trạng thái thành công.',
                'order' => $order,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Lỗi khi cập nhật trạng thái đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
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
    public function byUser($userId)
    {
        // Xác định khoảng thời gian "hôm nay"
        $startDate = Carbon::today()->startOfDay();
        $endDate   = Carbon::today()->endOfDay();

        // Lấy tên user để hiển thị
        $userName = User::find($userId)->name ?? 'Unknown';

        // Chỉ lấy các order của user trong ngày hôm nay
        $orders = Order::where('id_user', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.order.by_user', compact('orders', 'userName'));
    }
}
