<?php

namespace App\Services;

use App\Jobs\ExpireOrder;
use App\Mail\OrderPlacedMail;
use App\Models\Order;
use App\Models\Order_detail;
use App\Models\Order_status;
use App\Models\OrderCancellation;
use App\Models\OrderCancellationReason;
use App\Models\OrderExpire;
use App\Models\Product_variant;
use App\Models\User;
use App\Models\UserRestriction;
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    protected $order;
    protected $orderStatus;
    protected $orderExpire;
    protected $paymentService;
    protected $notificationService;
    protected $orderCancellationReason;
    protected $voucherService;
    protected $orderStatusService;
    protected $userRestriction;

    public function __construct(
        Order $order,
        PaymentService $paymentService,
        Order_status $orderStatus,
        OrderExpire $orderExpire,
        NotificationService $notificationService,
        OrderCancellationReason $orderCancellationReason,
        VoucherService $voucherService,
        OrderStatusService $orderStatusService,
        UserRestriction $userRestriction
    ) {
        $this->userRestriction = $userRestriction;
        $this->orderCancellationReason = $orderCancellationReason;
        $this->orderStatus = $orderStatus;
        $this->orderExpire = $orderExpire;
        $this->paymentService = $paymentService;
        $this->order = $order;
        $this->notificationService = $notificationService;
        $this->voucherService = $voucherService;
        $this->orderStatusService = $orderStatusService;
    }

    public function createOrder(array $requestData)
    {
        return DB::transaction(function () use ($requestData) {
            try {
                $validator = Validator::make($requestData, [
                    'data_user' => 'required|array',
                    'data_user.name' => 'required|string|max:255',
                    'data_user.phone' => 'required|string|max:20|regex:/^[0-9]{10,15}$/',
                    'data_user.email' => 'required|email|max:255',
                    'data_address' => 'required|array',
                    'data_address.province' => 'required|string|max:50',
                    'data_address.name_province' => 'required|string|max:255',
                    'data_address.district' => 'required|string|max:50',
                    'data_address.name_district' => 'required|string|max:255',
                    'data_address.ward' => 'required|string|max:50',
                    'data_address.name_ward' => 'required|string|max:255',
                    'data_address.address_detail' => 'required|string|max:500',
                    'data_order' => 'required|array',
                    'data_order.order_note' => 'nullable|string|max:1000',
                    'data_order.payment_method' => 'required|string|in:COD,VNPAY',
                ], [
                    'data_user.name.required' => 'Tên người dùng là bắt buộc.',
                    'data_user.email.required' => 'Email là bắt buộc.',
                    'data_user.email.email' => 'Email không đúng định dạng.',
                    'data_user.phone.regex' => 'Số điện thoại không hợp lệ.',
                    'data_user.phone.required' => 'Số điện thoại là bắt buộc.',
                    'data_address.*.required' => 'Thông tin địa chỉ không được để trống.',
                    'data_order.payment_method.required' => 'Phương thức thanh toán là bắt buộc.',
                    'data_order.payment_method.in' => 'Phương thức thanh toán không hợp lệ.',
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $checkoutData = session('checkout_data');
                if (!$checkoutData) {
                    throw new \Exception('Dữ liệu đơn hàng đã được dùng, hoặc hết hạn!');
                }

                $validator = Validator::make($checkoutData, [
                    'variants' => 'required|array|min:1',
                    'variants.*.id_variant' => 'required|integer|exists:product_variants,id',
                    'variants.*.price' => 'required|numeric|min:0',
                    'variants.*.quantity' => 'required|integer|min:1',
                    'coupon' => 'nullable|string|max:50',
                ], [
                    'variants.*.id_variant.required' => 'ID biến thể sản phẩm là bắt buộc.',
                    'variants.*.id_variant.exists' => 'Biến thể sản phẩm không tồn tại.',
                    'variants.*.price.required' => 'Giá sản phẩm là bắt buộc.',
                    'variants.*.price.min' => 'Giá sản phẩm phải lớn hơn hoặc bằng 0.',
                    'variants.*.quantity.required' => 'Số lượng sản phẩm là bắt buộc.',
                    'variants.*.quantity.min' => 'Số lượng sản phẩm phải lớn hơn 0.',
                ]);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $dataUser = $requestData['data_user'];
                $dataAddress = $requestData['data_address'];
                $dataOrder = $requestData['data_order'];
                $variants = $checkoutData['variants'];
                $coupon = $checkoutData['coupon'];

                $subtotal = 0;
                $variantIds = [];
                foreach ($variants as $variant) {
                    $subtotal += $variant['price'] * $variant['quantity'];
                    $variantIds[] = $variant['id_variant'];
                }

                $voucher = null;
                $discount = 0;
                if ($coupon) {
                    $voucherResult = $this->voucherService->applyCoupon($coupon, $variantIds, $subtotal);
                    if (!$voucherResult['success']) {
                        throw new \Exception($voucherResult['message']);
                    }
                    $discount = $voucherResult['discount'];
                    $voucher = Voucher::where('code', $coupon)
                        ->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->where('quantity', '>', 0)
                        ->first();
                    if (!$voucher) {
                        throw new \Exception('Không tìm thấy voucher hợp lệ trong cơ sở dữ liệu');
                    }
                }

                $shipping = 0;
                $total = $subtotal - $discount + $shipping;
                $orderCode = 'ORD-' . uniqid();

                $order = new Order();
                $order->code = $orderCode;
                $order->id_user = auth()->id() ?? null;
                $order->user_data = json_encode($dataUser);
                $order->address_data = json_encode($dataAddress);
                $order->voucher_data = $voucher
                    ? json_encode(array_merge($voucher->toArray(), ['discount' => $discount]))
                    : null;
                $order->note = $dataOrder['order_note'];
                $order->subtotal = $subtotal;
                $order->shipping = $shipping;
                $order->total = $total;
                $order->payment_method = $dataOrder['payment_method'];
                $order->payment_status = 'pending';
                $orderStatus = Order_status::where('name', 'Awaiting Payment')->first();
                if (!$orderStatus) {
                    throw new \Exception('Không tìm thấy trạng thái đơn hàng "Awaiting Payment"');
                }
                $order->id_order_status = $orderStatus->id;
                $order->save();

                foreach ($variants as $inputVariant) {
                    $variant = Product_variant::where('id', $inputVariant['id_variant'])->lockForUpdate()->first();
                    if (!$variant) {
                        throw new \Exception("Biến thể sản phẩm với ID {$inputVariant['id_variant']} không tồn tại");
                    }
                    if ($variant->quantity < $inputVariant['quantity']) {
                        throw new \Exception("Số lượng sản phẩm không đủ cho biến thể ID {$inputVariant['id_variant']}. Hiện có: {$variant->quantity}, Yêu cầu: {$inputVariant['quantity']}");
                    }
                    $variant->decrement('quantity', $inputVariant['quantity']);
                    $orderDetail = new Order_detail();
                    $orderDetail->id_order = $order->id;
                    $orderDetail->id_variant = $inputVariant['id_variant'];
                    $orderDetail->variant_data = json_encode($inputVariant);
                    $orderDetail->quantity = $inputVariant['quantity'];
                    $orderDetail->unit_price = $inputVariant['price'];
                    $orderDetail->total = $inputVariant['price'] * $inputVariant['quantity'];
                    $orderDetail->save();
                }
                $orderDetails = $order->detailsOrder();

                // Gửi email qua queue cho người dùng
                $userEmail = $orderDetails['user_email'];
                if ($userEmail && $userEmail !== 'N/A') {
                    try {
                        Mail::to($userEmail)->queue(new OrderPlacedMail($orderDetails));
                        Log::info('Đã xếp hàng email xác nhận đơn hàng', [
                            'order_id' => $order->id,
                            'email' => $userEmail,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Lỗi khi xếp hàng email xác nhận đơn hàng: ' . $e->getMessage(), [
                            'order_id' => $order->id,
                            'email' => $userEmail,
                        ]);
                    }
                }
                // Cập nhật giỏ hàng
                $cartService = app(CartService::class);
                $cartService->updateCartAfterOrder($variants, auth()->id());

                // Gửi thông báo cho người dùng (nếu có đăng nhập)
                // if ($order->id_user) {
                //     $this->notificationService->createOrderNotification(
                //         $order->id_user,
                //         $order->id,
                //         'Đơn hàng mới được tạo',
                //         "Đơn hàng #{$order->code} đã được tạo thành công. Vui lòng kiểm tra chi tiết."
                //     );
                // }

                // Gửi thông báo đến admin
                $this->notificationService->sendAdmin([
                    'title' => 'Đơn hàng mới',
                    'message' => "Đơn hàng #{$order->code} vừa được tạo. Vui lòng kiểm tra và xử lý.",
                    'type' => 'admin',
                    'category' => 'order',
                    'priority' => 'high',
                    'goto_id' => $order->id,
                    'goto_route' => 'admin.orders.detail',
                    'expires_at' => now()->addDays(7),
                ]);

                // Gửi thông báo đến employee
                $this->notificationService->sendEmployee([
                    'title' => 'Đơn hàng mới',
                    'message' => "Đơn hàng #{$order->code} vừa được tạo. Vui lòng kiểm tra và xử lý.",
                    'type' => 'employee',
                    'category' => 'order',
                    'priority' => 'high',
                    'goto_id' => $order->id,
                    'goto_route' => 'employee.orders.detail',
                    'expires_at' => now()->addDays(7),
                ]);

                // Xử lý voucher nếu có
                if ($voucher && $discount > 0) {
                    $voucher->total_usage += 1;
                    $voucher->quantity -= 1;
                    $voucher->save();
                }

                session()->forget('checkout_data');
                return $order;
            } catch (ValidationException $e) {
                throw new \Exception($e->validator->errors()->first());
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi tạo đơn hàng: ' . $e->getMessage());
            }
        });
    }

    public function handleCreateOrder($request)
    {
        try {
            $order = $this->createOrder($request->all());
            $response = $this->orderStatusService->updateInitialStatus($order);
            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công',
                'data' => $response
            ], 201);
        } catch (ValidationException $e) {
            Log::error('Validation error when creating order: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo đơn hàng: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ], 400);
        }
    }
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
    public function cancelOrder($orderId, $reasonId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                throw new \Exception('Bạn cần đăng nhập để hủy đơn hàng.');
            }

            if ($user->isLocked()) {
                throw new \Exception('Tài khoản của bạn đã bị khóa do vi phạm chính sách hủy đơn.');
            }

            $cancelLimit = 3;
            $cancelCount = $this->getCancelCount($user->id);
            if ($cancelCount >= $cancelLimit) {
                $this->restrictUser($user->id);
                Auth::logout();
                throw new \Exception('Bạn đã hủy quá số đơn hàng cho phép (3 lần/ngày). Tài khoản bị khóa 48 giờ.');
            }

            if (!$this->canCancelOrder($orderId)) {
                throw new \Exception('Đơn hàng không thể hủy ở trạng thái hiện tại.');
            }

            $order = $this->order->findOrFail($orderId);

            if (!OrderCancellationReason::find($reasonId)) {
                throw new \Exception('Lý do hủy không hợp lệ.');
            }

            $currentStatusId = $order->id_order_status;
            $cancelRequestedId = Order_status::where('name', 'Cancel Requested')->value('id');
            $order->update(['id_order_status' => $cancelRequestedId]);

            OrderCancellation::create([
                'order_id' => $orderId,
                'reason_id' => $reasonId,
                'from' => 'client',
                'status' => 'pending',
                'previous_status_id' => $currentStatusId,
            ]);



            Cache::forget("user_cancel_count_{$user->id}");
            return $order;
        } catch (\Exception $e) {
            Log::error('Lỗi trong cancelOrder: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function isUserRestricted($userId)
    {
        return UserRestriction::where('user_id', $userId)
            ->where('restriction_type', 'cancel_order')
            ->where('expires_at', '>', now())
            ->exists();
    }

    protected function restrictUser($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_lock = true;
        $user->save();

        $this->userRestriction->create([
            'user_id' => $userId,
            'restriction_type' => 'cancel_order',
            'restricted_at' => now(),
            'expires_at' => now()->addHours(48),
        ]);



        Log::info("Tài khoản ID {$userId} bị khóa do hủy đơn quá giới hạn.");
    }

    protected function getCancelCount($userId)
    {
        $cacheKey = "user_cancel_count_{$userId}";
        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($userId) {
            return OrderCancellation::where('from', 'client')
                ->where('created_at', '>=', now()->subDay())
                ->whereHas('order', function ($q) use ($userId) {
                    $q->where('id_user', $userId);
                })
                ->count();
        });
    }

    public function canCancelOrder($orderId)
    {
        try {
            $order = $this->order->findOrFail($orderId);
            $nonCancellableStatuses = [
                'Shipping',
                'Delivered',
                'Cancelled',
                'Refunded',
                'Cancel Requested',
                'Cancel Under Review',
                'Cancel Approved',
                'Return Requested',
                'Return Under Review',
                'Return Approved',
                'Return Rejected',
                'Failed Delivery'
            ];
            if (in_array($order->orderStatus->name, $nonCancellableStatuses)) {
                return false;
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi trong canCancelOrder: ' . $e->getMessage());
            return false;
        }
    }
    protected function validateSearchInput(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255|min:3',
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

            'search.min' => 'Từ khóa tìm kiếm phải có ít nhất 3 ký tự.',
            'group_status.in' => 'Nhóm trạng thái không hợp lệ.',
            'status_id.exists' => 'Trạng thái đơn hàng không hợp lệ.',
            'min_total.max' => 'Tổng giá trị tối thiểu không được vượt quá 1 tỷ VNĐ.',
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

        // Kiểm tra khoảng thời gian tối đa
        $validator->after(function ($validator) use ($request) {
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateFrom = Carbon::parse($request->date_from);
                $dateTo = Carbon::parse($request->date_to);
                if ($dateTo->diffInYears($dateFrom) > 1) {
                    $validator->errors()->add('date_to', 'Khoảng thời gian không được vượt quá 1 năm.');
                }
            }
        });

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        return [];
    }
    public function searchOrders(Request $request, $perPage = 10)
    {
        $user = Auth::user();
        if (!$user) {
            throw new \Exception('User not authenticated.');
        }

        // Validate dữ liệu đầu vào
        $validationResult = $this->validateSearchInput($request);
        if (!empty($validationResult['errors'])) {
            Log::warning('Validation failed in searchOrders', [
                'errors' => $validationResult['errors']->toArray(),
                'request' => $request->all(),
            ]);
            return ['errors' => $validationResult['errors']];
        }

        // Truy vấn chính cho danh sách đơn hàng
        $query = Order::where('id_user', $user->id)->with('orderStatus');

        // Áp dụng bộ lọc tìm kiếm
        if ($request->filled('search')) {
            $search = trim($request->input('search')); // Làm sạch chuỗi tìm kiếm
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
        $metricQuery = Order::where('id_user', $user->id);

        // Áp dụng các bộ lọc tương tự cho metricQuery
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $metricQuery->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.name')) LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(user_data, '$.email')) LIKE ?", ["%{$search}%"]);
            });
        }
        if ($request->filled('group_status') && $request->group_status !== 'All') {
            $metricQuery->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
            });
        }
        if ($request->filled('status_id')) {
            $metricQuery->where('id_order_status', $request->status_id);
        }
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
        $groupStatuses = Cache::remember('group_statuses', 60 * 60 * 24, function () {
            return Order_status::select('group_status')
                ->distinct()
                ->whereNotNull('group_status')
                ->pluck('group_status')
                ->toArray();
        });

        // Cache số lượng đơn hàng theo group_status
        $groupStatusCounts = Cache::remember('group_status_counts_' . md5(json_encode($request->only([
            'search',
            'group_status',
            'status_id',
            'min_total',
            'max_total',
            'min_shipping',
            'max_shipping',
            'date_from',
            'date_to'
        ]))), 60 * 5, function () use ($dateFrom, $dateTo, $groupStatuses, $request, $user) {
            $counts = [];
            $query = Order::selectRaw('order_statuses.group_status, COUNT(*) as count')
                ->where('orders.id_user', $user->id)
                ->join('order_statuses', 'orders.id_order_status', '=', 'order_statuses.id');

            // Áp dụng bộ lọc thời gian
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
                $search = trim($request->input('search'));
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
        $statuses = Cache::remember('order_statuses', 60 * 60 * 24, function () {
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

    public function listReason()
    {
        return $this->orderCancellationReason->query()->select('id', 'reason')->where('to', 'client')->orderBy('id', 'DESC')->get();
    }
}