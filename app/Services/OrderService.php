<?php

namespace App\Services;

use App\Jobs\ExpireOrder;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\VoucherService;
use Illuminate\Support\Facades\Cache;

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

                if ($voucher && $discount > 0) {
                    $voucher->total_usage += 1;
                    $voucher->quantity -= 1;
                    $voucher->save();
                }
                if ($order->id_user) {
                    $this->notificationService->sendPrivate([
                        'title' => 'Đơn hàng mới được tạo',
                        'message' => "Đơn hàng #{$order->code} đã được tạo thành công. Vui lòng kiểm tra chi tiết.",
                        'to_user_id' => $order->id_user,
                        'type' => 'private',
                        'category' => 'order',
                        'priority' => 'medium',
                        'goto_id' => $order->id,
                        'goto_route' => 'client.order.detail',
                        'expires_at' => now()->addDays(7),
                    ]);
                }

                // Gửi thông báo đến admin và employee
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
            $this->notificationService->sendPrivate([
                'title' => 'Yêu cầu hủy đơn hàng',
                'message' => "Yêu cầu hủy đơn hàng #{$order->code} đã được gửi. Đội ngũ sẽ xem xét trong vòng 24 giờ.",
                'to_user_id' => $user->id,
                'type' => 'private',
                'category' => 'order',
                'priority' => 'medium',
                'goto_id' => $order->id,
                'goto_route' => 'client.order.detail',
                'expires_at' => now()->addDays(7),
            ]);

            $this->notificationService->sendAdmin([
                'title' => 'Yêu cầu hủy đơn hàng mới',
                'message' => "Đơn hàng #{$order->code} đã được yêu cầu hủy bởi khách hàng. Vui lòng xem xét.",
                'type' => 'admin',
                'category' => 'order',
                'priority' => 'high',
                'goto_id' => $order->id,
                'goto_route' => 'admin.orders.detail',
                'expires_at' => now()->addDays(7),
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

    public function searchOrders(Request $request, $perPage = 10)
    {
        $user = Auth::user();
        $query = Order::where('id_user', $user->id)->with('orderStatus');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('user_data->name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('group_status') && $request->group_status !== 'All') {
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
            });
        }

        if ($request->filled('min_total') && is_numeric($request->min_total)) {
            $query->where('total', '>=', $request->min_total);
        }
        if ($request->filled('max_total') && is_numeric($request->max_total)) {
            $query->where('total', '<=', $request->max_total);
        }

        if ($request->filled('min_shipping') && is_numeric($request->min_shipping)) {
            $query->where('shipping', '>=', $request->min_shipping);
        }
        if ($request->filled('max_shipping') && is_numeric($request->max_shipping)) {
            $query->where('shipping', '<=', $request->max_shipping);
        }

        if ($request->filled('group_by')) {
            $query->orderBy($request->group_by);
        }

        $totalOrders = Order::where('id_user', $user->id)->count();
        $openOrders = Order::where('id_user', $user->id)
            ->whereHas('orderStatus', function ($q) {
                $q->whereIn('group_status', ['Awaiting Delivery', 'Shipping']);
            })->count();
        $averagePrice = Order::where('id_user', $user->id)->avg('total');

        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);
        $groupStatuses = Order_status::select('group_status')
            ->distinct()
            ->whereNotNull('group_status')
            ->pluck('group_status')
            ->toArray();

        $groupStatusCounts = [];
        foreach ($groupStatuses as $group) {
            $groupStatusCounts[$group] = Order::where('id_user', $user->id)
                ->whereHas('orderStatus', function ($q) use ($group) {
                    $q->where('group_status', $group);
                })->count();
        }

        return compact(
            'orders',
            'totalOrders',
            'openOrders',
            'averagePrice',
            'groupStatuses',
            'groupStatusCounts'
        );
    }

    public function listReason()
    {
        return $this->orderCancellationReason->query()->select('id', 'reason')->where('to', 'client')->orderBy('id', 'DESC')->get();
    }
}
