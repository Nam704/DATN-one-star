<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Order_status;
use App\Models\OrderCancellation;
use App\Models\Product_variant;
use App\Models\User;
use App\Models\UserRestriction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OrderServiceManager
{
    protected $orderService;
    protected $notificationService;
    protected $userRestriction;

    public function __construct(
        OrderService $orderService,
        NotificationService $notificationService,
        UserRestriction $userRestriction
    ) {
        $this->orderService = $orderService;
        $this->notificationService = $notificationService;
        $this->userRestriction = $userRestriction;
    }

    /**
     * Mở khóa tài khoản người dùng
     */
    public function unrestrictUser($userId)
    {
        try {
            $restriction = $this->userRestriction
                ->where('user_id', $userId)
                ->where('restriction_type', 'cancel_order')
                ->where('expires_at', '>', now())
                ->first();

            if (!$restriction) {
                throw new \Exception('Người dùng không bị khóa hủy đơn hàng.');
            }

            $user = User::findOrFail($userId);
            $user->is_lock = false;
            $user->save();

            $restriction->delete();



            Log::info("Tài khoản ID {$userId} được mở khóa bởi admin ID: " . auth()->id());

            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi trong unrestrictUser: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Lấy danh sách người dùng bị khóa
     */
    public function getRestrictedUsers(Request $request, $perPage = 10)
    {
        try {
            $query = $this->userRestriction->with('user')
                ->where('restriction_type', 'cancel_order')
                ->where('expires_at', '>', now());

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            }

            $sortBy = $request->input('sort_by', 'restricted_at');
            $sortOrder = $request->input('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $restrictions = $query->paginate($perPage);

            return [
                'restrictions' => $restrictions,
                'total' => $restrictions->total(),
            ];
        } catch (\Exception $e) {
            Log::error('Lỗi trong getRestrictedUsers: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Xử lý yêu cầu hủy đơn hàng
     */
    public function processCancelRequest($orderId, $action, $adminNote = null)
    {
        try {
            $order = Order::findOrFail($orderId);
            $currentStatus = $order->orderStatus->name;

            if (!in_array($currentStatus, ['Cancel Requested', 'Cancel Under Review'])) {
                throw new \Exception('Yêu cầu hủy không ở trạng thái phù hợp để xử lý.');
            }

            $cancellation = $order->orderCancellations()->latest()->first();
            $previousStatus = $cancellation->previous_status_id
                ?? Order_status::where('name', 'Pending')->value('id');

            if (!$previousStatus || !Order_status::find($previousStatus)) {
                Log::warning("Không tìm thấy previous_status_id hợp lệ cho đơn hàng ID: {$orderId}. Dùng trạng thái mặc định Pending.");
                $previousStatus = Order_status::where('name', 'Pending')->value('id');
            }

            if ($action === 'approve') {
                DB::transaction(function () use ($order) {
                    $newStatusId = Order_status::where('name', 'Cancel Approved')->value('id');
                    $order->update(['id_order_status' => $newStatusId]);

                    foreach ($order->orderDetails as $detail) {
                        $variant = Product_variant::find($detail->id_variant);
                        if (!$variant) {
                            throw new \Exception("Biến thể sản phẩm ID {$detail->id_variant} không tồn tại.");
                        }
                        $variant->increment('quantity', $detail->quantity);
                    }

                    $finalStatusId = Order_status::where('name', 'Cancelled')->value('id');
                    $order->update(['id_order_status' => $finalStatusId]);

                    OrderCancellation::where('order_id', $order->id)->update(['status' => 'approved']);
                });

                $message = "Yêu cầu hủy đơn hàng {$order->code} đã được phê duyệt.";
            } elseif ($action === 'reject') {
                if (empty($adminNote)) {
                    throw new \Exception('Ghi chú là bắt buộc khi từ chối hủy đơn.');
                }

                DB::transaction(function () use ($order, $previousStatus, $adminNote) {
                    $rejectStatusId = Order_status::where('name', 'Cancel Rejected')->value('id');
                    $order->update(['id_order_status' => $rejectStatusId]);

                    $order->update(['id_order_status' => $previousStatus]);

                    OrderCancellation::where('order_id', $order->id)->update([
                        'status' => 'rejected',
                        'note' => $adminNote,
                    ]);
                });

                $message = "Yêu cầu hủy đơn hàng {$order->code} đã bị từ chối. Lý do: {$adminNote}";
            } elseif ($action === 'review') {
                $reviewStatusId = Order_status::where('name', 'Cancel Under Review')->value('id');
                $order->update(['id_order_status' => $reviewStatusId]);
                OrderCancellation::where('order_id', $order->id)->update(['status' => 'under_review']);

                $message = "Yêu cầu hủy đơn hàng {$order->code} đang được xem xét lại.";
            } else {
                throw new \Exception('Hành động không hợp lệ.');
            }

            Log::info("Admin xử lý yêu cầu hủy đơn hàng ID: {$orderId}", [
                'action' => $action,
                'admin_id' => auth()->id(),
                'status_before' => $currentStatus,
                'status_after' => $action === 'approve' ? 'Cancelled' : Order_status::find($previousStatus)->name,
                'note' => $adminNote ?? 'Không có ghi chú',
            ]);

            return $order;
        } catch (\Exception $e) {
            Log::error('Lỗi trong processCancelRequest: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateOrderStatus($orderId)
    {
        $order = Order::findOrFail($orderId);
        $currentStatus = $order->orderStatus;

        $nextStatus = $currentStatus->nextStatus;

        if (!$nextStatus) {
            return null;
        }

        $order->id_order_status = $nextStatus->id;
        $order->save();

        return $order;
    }

    /**
     * Tìm kiếm đơn hàng
     */
    public function searchOrders(Request $request, $perPage = 10)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'group_status' => ['nullable', Rule::in(array_merge(['All'], Cache::remember('group_statuses', 60 * 60 * 24, function () {
                return Order_status::select('group_status')
                    ->distinct()
                    ->whereNotNull('group_status')
                    ->pluck('group_status')
                    ->toArray();
            })))],
            'status_id' => 'nullable|integer|exists:order_statuses,id|min:1',
            'min_total' => 'nullable|numeric|min:0|max:100000000',
            'max_total' => 'nullable|numeric|min:0|max:100000000|gte:min_total',
            'min_shipping' => 'nullable|numeric|min:0|max:1000000',
            'max_shipping' => 'nullable|numeric|min:0|max:1000000|gte:min_shipping',
            'date_from' => 'nullable|date|before_or_equal:today|after_or_equal:' . now()->subYears(2)->toDateString(),
            'date_to' => 'nullable|date|after_or_equal:date_from|before_or_equal:today',
            'sort_by' => 'nullable|string|in:created_at,total,code',
            'sort_order' => 'nullable|string|in:asc,desc',
        ], [
            'group_status.in' => 'Nhóm trạng thái không hợp lệ.',
            'status_id.exists' => 'Trạng thái đơn hàng không hợp lệ.',
            'min_total.max' => 'Tổng giá trị tối thiểu không được vượt quá 100tr VNĐ.',
            'max_total.gte' => 'Tổng giá trị tối đa phải lớn hơn hoặc bằng tổng giá trị tối thiểu.',
            'min_shipping.max' => 'Phí vận chuyển tối thiểu không được vượt quá 1 triệu VNĐ.',
            'max_shipping.gte' => 'Phí vận chuyển tối đa phải lớn hơn hoặc bằng phí vận chuyển tối thiểu.',
            'date_from.before_or_equal' => 'Ngày bắt đầu không được trong tương lai.',
            'date_from.after_or_equal' => 'Ngày bắt đầu không được trước 2 năm.',
            'date_to.after_or_equal' => 'Ngày kết thúc phải sau hoặc bằng ngày bắt đầu.',
            'date_to.before_or_equal' => 'Ngày kết thúc không được trong tương lai.',
            'sort_by.in' => 'Cột sắp xếp không hợp lệ.',
            'sort_order.in' => 'Thứ tự sắp xếp không hợp lệ.',
        ]);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $query = Order::with('orderStatus');

        // Áp dụng bộ lọc tìm kiếm
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.name')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.email')) LIKE ?", ["%{$search}%"]);
            });
        }

        // Áp dụng bộ lọc group_status
        if ($request->filled('group_status') && $request->group_status !== 'All') {
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
            });
        }

        // Áp dụng bộ lọc status_id
        if ($request->filled('status_id')) {
            $query->where('id_order_status', $request->status_id);
        }

        // Áp dụng bộ lọc total và shipping
        if ($request->filled('min_total')) {
            $query->where('total', '>=', $request->min_total);
        }
        if ($request->filled('max_total')) {
            $query->where('total', '<=', $request->max_total);
        }
        if ($request->filled('min_shipping')) {
            $query->where('shipping', '>=', $request->min_shipping);
        }
        if ($request->filled('max_shipping')) {
            $query->where('shipping', '<=', $request->max_shipping);
        }

        // Áp dụng bộ lọc thời gian
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

        // Sắp xếp
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Tính toán các chỉ số dựa trên cùng bộ lọc
        $metricQuery = Order::query();

        // Áp dụng bộ lọc group_status cho metricQuery
        if ($request->filled('group_status') && $request->group_status !== 'All') {
            $metricQuery->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
            });
        }

        // Áp dụng bộ lọc status_id
        if ($request->filled('status_id')) {
            $metricQuery->where('id_order_status', $request->status_id);
        }

        // Áp dụng bộ lọc total và shipping
        if ($request->filled('min_total')) {
            $metricQuery->where('total', '>=', $request->min_total);
        }
        if ($request->filled('max_total')) {
            $metricQuery->where('total', '<=', $request->max_total);
        }
        if ($request->filled('min_shipping')) {
            $metricQuery->where('shipping', '>=', $request->min_shipping);
        }
        if ($request->filled('max_shipping')) {
            $metricQuery->where('shipping', '<=', $request->max_shipping);
        }

        // Áp dụng bộ lọc thời gian
        if ($dateFrom && $dateTo) {
            $dateToEnd = Carbon::parse($dateTo)->endOfDay();
            $metricQuery->whereBetween('created_at', [$dateFrom, $dateToEnd]);
        } elseif ($dateFrom) {
            $metricQuery->whereDate('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $dateToEnd = Carbon::parse($dateTo)->endOfDay();
            $metricQuery->whereDate('created_at', '<=', $dateToEnd);
        }

        // Tính toán các chỉ số
        $totalOrders = $metricQuery->count();
        $openOrders = $metricQuery->whereHas('orderStatus', function ($q) {
            $q->whereNotNull('next_status_id');
        })->count();
        $averagePrice = $metricQuery->avg('total') ?? 0;
        $totalRevenue = $metricQuery->sum('total') ?? 0;

        // Phân trang
        $orders = $query->paginate($perPage);

        // Cache danh sách group_status
        $groupStatuses = Cache::remember('group_statuses', 60 * 60, function () {
            return Order_status::select('group_status')
                ->distinct()
                ->whereNotNull('group_status')
                ->pluck('group_status')
                ->toArray();
        });

        // Cache số lượng đơn hàng theo group_status
        $groupStatusCounts = Cache::remember('group_status_counts_' . md5(json_encode($request->all())), 60, function () use ($dateFrom, $dateTo, $groupStatuses, $request) {
            $counts = [];
            $query = Order::selectRaw('order_statuses.group_status, COUNT(*) as count')
                ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id');

            // Áp dụng bộ lọc thời gian cho groupStatusCounts
            if ($dateFrom && $dateTo) {
                $dateToEnd = Carbon::parse($dateTo)->endOfDay();
                $query->whereBetween('orders.created_at', [$dateFrom, $dateToEnd]);
            } elseif ($dateFrom) {
                $query->whereDate('orders.created_at', '>=', $dateFrom);
            } elseif ($dateTo) {
                $dateToEnd = Carbon::parse($dateTo)->endOfDay();
                $query->whereDate('orders.created_at', '<=', $dateToEnd);
            }

            // Áp dụng bộ lọc tìm kiếm
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.name')) LIKE ?", ["%{$search}%"])
                        ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.email')) LIKE ?", ["%{$search}%"]);
                });
            }

            $results = $query->groupBy('order_statuses.group_status')->get();

            foreach ($groupStatuses as $group) {
                $counts[$group] = $results->firstWhere('group_status', $group)->count ?? 0;
            }

            return $counts;
        });

        // Cache danh sách trạng thái
        $statuses = Cache::remember('order_statuses', 60 * 60, function () {
            return Order_status::select('id', 'name')->get();
        });

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
