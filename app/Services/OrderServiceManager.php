<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_status;
use App\Models\OrderCancellation;
use App\Models\Product_variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OrderServiceManager
{
    protected $orderService;
    protected $notificationService;
    public function __construct(
        OrderService $orderService,
        NotificationService $notificationService
    ) {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
    }

    public function processCancelRequest($orderId, $action,)
    {
        try {
            $order = Order::findOrFail($orderId);
            $currentStatus = $order->orderStatus->name;

            if (!in_array($currentStatus, ['Cancel Requested', 'Cancel Under Review'])) {
                throw new \Exception('Yêu cầu hủy không ở trạng thái phù hợp để xử lý.');
            }

            if ($action === 'review') {
                $newStatusId = Order_status::where('name', 'Cancel Under Review')->value('id');
                $order->update(['id_order_status' => $newStatusId]);
            } elseif ($action === 'approve') {
                $newStatusId = Order_status::where('name', 'Cancel Approved')->value('id');
                $order->update(['id_order_status' => $newStatusId]);

                // Hoàn kho sản phẩm
                foreach ($order->orderDetails as $detail) {
                    Product_variant::where('id', $detail->id_variant)
                        ->increment('quantity', $detail->quantity);
                }

                // Cập nhật trạng thái cuối cùng thành Cancelled
                $finalStatusId = Order_status::where('name', 'Cancelled')->value('id');
                $order->update(['id_order_status' => $finalStatusId]);

                // Cập nhật trạng thái order_cancellations
                OrderCancellation::where('order_id', $orderId)->update(['status' => 'approved']);
            } else {
                throw new \Exception('Hành động không hợp lệ.');
            }

            // Gửi thông báo cho người dùng
            $message = $action === 'approve'
                ? "Yêu cầu hủy đơn hàng {$order->code} đã được phê duyệt."

                : "Yêu cầu hủy đơn hàng {$order->code} đang được xem xét.";
            $userNotification = [
                'title' => 'Cập nhật yêu cầu hủy',
                'message' => $message,
                'from_user_id' => null,
                'to_user_id' => $order->id_user,
                'type' => 'orders',
                'status' => 'unread',
                'goto_id' => $order->id,
            ];
            $this->notificationService->sendPrivate($userNotification);

            return $order;
        } catch (\Exception $e) {
            Log::error('Lỗi trong processCancelRequest: ' . $e->getMessage());
            throw $e;
        }
    }
    public function updateOrderStatus($orderId)
    {

        $order = Order::findOrFail($orderId);
        $currentStatus = $order->orderStatus;  // Lấy trạng thái hiện tại của đơn hàng

        // // Kiểm tra xem trạng thái hiện tại có trạng thái tiếp theo hợp lệ hay không
        $nextStatus = $currentStatus->nextStatus; // Lấy trạng thái tiếp theo từ bảng order_statuses

        if (!$nextStatus) {
            return null;
        }

        // Cập nhật trạng thái đơn hàng
        $order->id_order_status = $nextStatus->id;
        $order->save();

        return $order;
    }
    public function searchOrders(Request $request, $perPage = 10)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'group_status' => 'nullable|string',
            'status_id' => 'nullable|integer|exists:order_statuses,id',
            'min_total' => 'nullable|numeric|min:0',
            'max_total' => 'nullable|numeric|min:0',
            'min_shipping' => 'nullable|numeric|min:0',
            'max_shipping' => 'nullable|numeric|min:0',
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort_by' => 'nullable|string|in:created_at,total,code',
            'sort_order' => 'nullable|string|in:asc,desc',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Query chính để tìm kiếm đơn hàng
        $query = Order::with('orderStatus');

        // Tìm kiếm theo code, name, email
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.name')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.email')) LIKE ?", ["%{$search}%"]);
            });
        }



        // Lọc theo group_status
        if ($request->filled('group_status') && $request->group_status !== 'All') {
            Log::info('Filtering by group_status: ' . $request->group_status); // Debug
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
                Log::info('SQL Query: ' . $q->toSql()); // Debug SQL
            });
        }

        // Lọc theo status_id
        if ($request->filled('status_id')) {
            $query->where('id_order_status', $request->status_id);
        }

        // Lọc theo tổng tiền
        if ($request->filled('min_total')) {
            $query->where('total', '>=', $request->min_total);
        }
        if ($request->filled('max_total')) {
            $query->where('total', '<=', $request->max_total);
        }

        // Lọc theo phí vận chuyển
        if ($request->filled('min_shipping')) {
            $query->where('shipping', '>=', $request->min_shipping);
        }
        if ($request->filled('max_shipping')) {
            $query->where('shipping', '<=', $request->max_shipping);
        }

        // Lọc theo ngày tạo đơn hàng
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if ($dateFrom && $dateTo) {
            $dateToEnd = Carbon::parse($dateTo)->endOfDay();
            $query->whereBetween('created_at', [$dateFrom, $dateToEnd]);
        } elseif ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $dateToEnd = Carbon::parse($dateTo)->endOfDay();
            $query->whereDate('created_at', '<=', $dateToEnd);
        }
        // Bỏ logic mặc định lọc trong ngày hiện tại
        // Không thêm điều kiện ngày nếu không có date_from hoặc date_to

        // Sắp xếp kết quả
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Tính toán các chỉ số tổng hợp
        $metricQuery = Order::query();
        if ($dateFrom && $dateTo) {
            $dateToEnd = Carbon::parse($dateTo)->endOfDay();
            $metricQuery->whereBetween('created_at', [$dateFrom, $dateToEnd]);
        } elseif ($dateFrom) {
            $metricQuery->whereDate('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $dateToEnd = Carbon::parse($dateTo)->endOfDay();
            $metricQuery->whereDate('created_at', '<=', $dateToEnd);
        }
        // Không giới hạn ngày cho metricQuery nếu không có date_from/date_to

        $totalOrders = $metricQuery->count();
        $openOrders = $metricQuery->whereHas('orderStatus', function ($q) {
            $q->whereNotNull('next_status_id');
        })->count();
        $averagePrice = $metricQuery->avg('total') ?? 0;
        $totalRevenue = $metricQuery->sum('total') ?? 0;

        // Phân trang kết quả
        $orders = $query->paginate($perPage);

        // Lấy danh sách group_status duy nhất
        $groupStatuses = Cache::remember('group_statuses', 60 * 60, function () {
            return Order_status::select('group_status')
                ->distinct()
                ->whereNotNull('group_status')
                ->pluck('group_status')
                ->toArray();
        });

        // Tính số lượng đơn hàng cho mỗi group_status
        $groupStatusCounts = Cache::remember('group_status_counts_' . md5(json_encode($request->all())), 60, function () use ($dateFrom, $dateTo, $groupStatuses) {
            $counts = [];
            $query = Order::selectRaw('order_statuses.group_status, COUNT(*) as count')
                ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id');

            if ($dateFrom && $dateTo) {
                $dateToEnd = Carbon::parse($dateTo)->endOfDay();
                $query->whereBetween('orders.created_at', [$dateFrom, $dateToEnd]);
            } elseif ($dateFrom) {
                $query->whereDate('orders.created_at', '>=', $dateFrom);
            } elseif ($dateTo) {
                $dateToEnd = Carbon::parse($dateTo)->endOfDay();
                $query->whereDate('orders.created_at', '<=', $dateToEnd);
            }

            $results = $query->groupBy('order_statuses.group_status')->get();

            foreach ($groupStatuses as $group) {
                $counts[$group] = $results->firstWhere('group_status', $group)->count ?? 0;
            }

            return $counts;
        });

        // Lấy danh sách trạng thái cụ thể
        $statuses = Cache::remember('order_statuses', 60 * 60, function () {
            return Order_status::select('id', 'name')->get();
        });

        // Trả về dữ liệu
        return compact(
            'orders',
            'totalOrders',
            'openOrders',
            'averagePrice',
            'totalRevenue',
            'groupStatuses',
            'groupStatusCounts',
            'statuses'
        );
    }
}
