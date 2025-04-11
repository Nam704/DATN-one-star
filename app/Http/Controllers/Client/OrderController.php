<?php

namespace App\Http\Controllers\Client;

use App\Events\OrderNotification;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_status;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\OrderStatusService;
use App\Services\PaymentService;
use App\Services\RetryPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $orderService;
    protected $paymentService;
    protected $orderStatusService;
    protected $notificationService;
    protected $retryPaymentService;
    public function __construct(
        RetryPaymentService $retryPaymentService,
        OrderService $orderService,
        PaymentService $paymentService,
        NotificationService $notificationService,
        OrderStatusService $orderStatusService

    ) {
        $this->retryPaymentService = $retryPaymentService;
        $this->orderStatusService = $orderStatusService;
        $this->notificationService = $notificationService;
        $this->paymentService = $paymentService;
        $this->orderService = $orderService;
    }
    public function retryPayment($id)
    {
        $order = Order::findOrFail($id);

        if (!$order->canRetryPayment()) {
            return redirect()->back()->with('error', 'This order is not eligible for re-payment.');
        }

        // Redirect to VNPAY payment gateway (pseudo-code)
        $vnpayUrl = $this->generateVnpayPaymentUrl($order);
        return redirect($vnpayUrl);
    }

    private function generateVnpayPaymentUrl($order)
    {
        // Implement VNPAY payment URL generation logic here
        // Example: https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?params...
        return 'vnpay_payment_url';
    }
    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('id_user', $user->id)->with('orderStatus');

        // 1. Tìm kiếm dựa trên code hoặc tên khách hàng (search)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('user_data->name', 'like', "%{$search}%");
            });
        }

        // 2. Lọc theo trạng thái (status)
        if ($request->filled('status') && $request->status !== 'All') {
            $status = Order_status::where('name', $request->status)->first();
            if ($status) {
                $query->where('id_order_status', $status->id);
            }
        }

        // 3. Lọc theo nhóm trạng thái (group_status)
        if ($request->filled('group_status')) {
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
            });
        }

        // 4. Lọc theo tổng tiền (min_total và max_total)
        if ($request->filled('min_total') && is_numeric($request->min_total)) {
            $query->where('total', '>=', $request->min_total);
        }
        if ($request->filled('max_total') && is_numeric($request->max_total)) {
            $query->where('total', '<=', $request->max_total);
        }

        // 5. Lọc theo phí vận chuyển (min_shipping và max_shipping)
        if ($request->filled('min_shipping') && is_numeric($request->min_shipping)) {
            $query->where('shipping', '>=', $request->min_shipping);
        }
        if ($request->filled('max_shipping') && is_numeric($request->max_shipping)) {
            $query->where('shipping', '<=', $request->max_shipping);
        }

        // 6. Sắp xếp (group_by)
        if ($request->filled('group_by')) {
            $query->orderBy($request->group_by);
        }

        // 7. Tính toán các chỉ số tổng hợp (summary metrics)
        $totalOrders = Order::where('id_user', $user->id)->count();
        $openOrders = Order::where('id_user', $user->id)
            ->whereHas('orderStatus', function ($q) {
                $q->where('group_status', 'In Progress');
            })->count();
        $averagePrice = Order::where('id_user', $user->id)->avg('total');

        // 8. Phân trang kết quả
        $orders = $query->paginate(10);

        // 9. Lấy tất cả trạng thái để hiển thị trong dropdown
        $statuses = Order_status::all();

        // 10. Lấy danh sách group_status duy nhất
        $groupStatuses = Order_status::select('group_status')
            ->distinct()
            ->whereNotNull('group_status')
            ->pluck('group_status')
            ->toArray();

        // 11. Trả về view
        return view('client.user.index', compact('orders', 'totalOrders', 'openOrders', 'averagePrice', 'statuses', 'groupStatuses'));
    }
    // public function retryPayment(Request $request)
    // {
    //     $orderId = $request->input('order_id');
    //     // Log::info($orderId);
    //     $order = Order::find($orderId);
    //     if (!$order) {
    //         return response()->json(['message' => 'Order not found'], 404);
    //     }
    //     $result =   $this->retryPaymentService->retryPayment($orderId);
    //     return response()->json($result);
    // }
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            $order = $this->orderService->createOrder($data);
            $vnpayResponse = $this->orderStatusService->updateInitialStatus($order);

            $responseData = [
                'status' => 200,
                'message' => 'Đặt hàng thành công',
                'order' => $order,
            ];

            if ($vnpayResponse) {
                $responseData['redirect_url'] = $vnpayResponse['data'];
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    public function cancel(Request $request)
    {
        $order = $this->orderService->cancelOrder($request);
        event(new OrderNotification($order));

        return response()->json([
            'status' => 200,
            'message' => 'Đã hủy đơn hàng',

        ]);
    }
    function  check()
    {
        $data = [
            'title' => 'New Order',
            'message' => "New Order, vui lòng kiểm tra và xác nhận!",
            'from_user_id' => null,
            'to_user_id' => null,
            'type' => 'orders',
            'status' => 'unread',
            'goto_id' => null,
        ];
        $this->notificationService->sendPrivate($data);
    }
    public function detailOrder($id)
    {
        // Lấy đơn hàng với dữ liệu tối ưu từ hàm details
        $order = Order::findOrFail($id)->detailsOrder();
        // return $order;
        return view('client.orders.detail', ['order' => $order]);
    }
}
