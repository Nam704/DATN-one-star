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
use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Services\VoucherService;

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
    public function __construct(
        Order $order,
        PaymentService $paymentService,
        Order_status $orderStatus,
        OrderExpire $orderExpire,
        NotificationService $notificationService,
        OrderCancellationReason $orderCancellationReason,
        VoucherService $voucherService,
        OrderStatusService $orderStatusService

    ) {
        $this->orderCancellationReason = $orderCancellationReason;
        $this->orderStatus = $orderStatus;
        $this->orderExpire = $orderExpire;
        $this->paymentService = $paymentService;
        $this->order = $order;
        $this->notificationService = $notificationService;
        $this->voucherService = $voucherService;
        $this->orderStatusService = $orderStatusService;
        // Constructor logic
    }

    public function createOrder(array $requestData)
    {
        // Bắt đầu transaction để đảm bảo toàn vẹn dữ liệu
        return DB::transaction(function () use ($requestData) {
            // Bước 0: Validate dữ liệu đầu vào
            try {
                $validator = Validator::make($requestData, [
                    'data_user' => 'required|array',
                    'data_user.name' => 'required|string|max:255',
                    'data_user.phone' => 'required|string|max:20|regex:/^[0-9]{10,15}$/', // Số điện thoại 10-15 chữ số
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
                    'data_order.payment_method' => 'required|string|in:COD,VNPAY', // Các phương thức thanh toán hợp lệ
                ], [
                    // Thông báo lỗi tùy chỉnh (tùy chọn)
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

                // Validate checkout_data từ session
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
            } catch (ValidationException $e) {
                throw new \Exception($e->validator->errors()->first());
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi validate dữ liệu: ' . $e->getMessage());
            }

            // Bước 1: Chuẩn bị dữ liệu
            try {
                $dataUser = $requestData['data_user'];
                $dataAddress = $requestData['data_address'];
                $dataOrder = $requestData['data_order'];
                $variants = $checkoutData['variants'];
                $coupon = $checkoutData['coupon'];
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi chuẩn bị dữ liệu: ' . $e->getMessage());
            }

            // Bước 2: Tính subtotal trước để dùng cho voucher
            try {
                $subtotal = 0;
                $variantIds = [];
                foreach ($variants as $variant) {
                    $subtotal += $variant['price'] * $variant['quantity'];
                    $variantIds[] = $variant['id_variant'];
                }
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi tính subtotal: ' . $e->getMessage());
            }

            // Bước 3: Xử lý voucher (nếu có) bằng VoucherService
            $voucher = null;
            $discount = 0;
            if ($coupon) {
                try {
                    // Gọi VoucherService để áp dụng coupon
                    $voucherResult = $this->voucherService->applyCoupon($coupon, $variantIds, $subtotal);

                    if (!$voucherResult['success']) {
                        throw new \Exception($voucherResult['message']);
                    }

                    $discount = $voucherResult['discount'];

                    // Lấy thông tin voucher từ DB để lưu vào đơn hàng
                    $voucher = Voucher::where('code', $coupon)
                        ->where('status', 'active')
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->where('quantity', '>', 0)
                        ->first();

                    if (!$voucher) {
                        throw new \Exception('Không tìm thấy voucher hợp lệ trong cơ sở dữ liệu');
                    }
                } catch (\Exception $e) {
                    throw new \Exception('Lỗi khi xử lý voucher: ' . $e->getMessage());
                }
            }

            // Bước 4: Tính shipping (giả định = 0)
            $shipping = 0; // Thay đổi logic tính shipping nếu cần

            // Bước 5: Tính total
            $total = $subtotal - $discount + $shipping;

            // Bước 6: Tạo mã đơn hàng unique
            $orderCode = 'ORD-' . uniqid();

            // Bước 7: Lưu thông tin vào bảng orders
            try {
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
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi lưu thông tin đơn hàng: ' . $e->getMessage());
            }

            // Bước 8: Lưu thông tin vào bảng order_details và kiểm tra số lượng
            try {
                foreach ($variants as $inputVariant) {
                    // Lấy biến thể từ DB với khóa để tránh xung đột
                    $variant = Product_variant::where('id', $inputVariant['id_variant'])->lockForUpdate()->first();

                    if (!$variant) {
                        throw new \Exception("Biến thể sản phẩm với ID {$inputVariant['id_variant']} không tồn tại");
                    }

                    // Kiểm tra số lượng
                    if ($variant->quantity < $inputVariant['quantity']) {
                        throw new \Exception("Số lượng sản phẩm không đủ cho biến thể ID {$inputVariant['id_variant']}. Hiện có: {$variant->quantity}, Yêu cầu: {$inputVariant['quantity']}");
                    }

                    // Giảm số lượng trong kho
                    $variant->decrement('quantity', $inputVariant['quantity']);

                    // Lưu chi tiết đơn hàng
                    $orderDetail = new Order_detail();
                    $orderDetail->id_order = $order->id;
                    $orderDetail->id_variant = $inputVariant['id_variant'];
                    $orderDetail->variant_data = json_encode($inputVariant);
                    $orderDetail->quantity = $inputVariant['quantity'];
                    $orderDetail->unit_price = $inputVariant['price'];
                    $orderDetail->total = $inputVariant['price'] * $inputVariant['quantity'];
                    $orderDetail->save();
                }
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi lưu chi tiết đơn hàng: ' . $e->getMessage());
            }

            // Bước 9: Cập nhật thông tin voucher (nếu có)
            if ($voucher && $discount > 0) {
                try {
                    $voucher->total_usage += 1;
                    $voucher->quantity -= 1;
                    $voucher->save();
                } catch (\Exception $e) {
                    throw new \Exception('Lỗi khi cập nhật thông tin voucher: ' . $e->getMessage());
                }
            }

            // Bước 10: Xử lý sau khi tạo đơn hàng
            try {
                session()->forget('checkout_data');
            } catch (\Exception $e) {
                throw new \Exception('Lỗi khi xóa dữ liệu checkout trong session: ' . $e->getMessage());
            }

            return $order; // Trả về đơn hàng vừa tạo
        });
    }

    // Hàm xử lý response (nếu dùng trong API)
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
            if (!$this->canCancelOrder($orderId)) {
                throw new \Exception('Đơn hàng không thể hủy ở trạng thái hiện tại.');
            }

            $order = $this->order->findOrFail($orderId);

            // Kiểm tra lý do hủy
            if (!OrderCancellationReason::find($reasonId)) {
                throw new \Exception('Lý do hủy không hợp lệ.');
            }

            // Cập nhật trạng thái thành "Cancel Requested"
            $cancelRequestedId = Order_status::where('name', 'Cancel Requested')->value('id');
            $order->update(['id_order_status' => $cancelRequestedId]);

            // Ghi nhận lý do hủy
            OrderCancellation::create([
                'order_id' => $orderId,
                'reason_id' => $reasonId,
                'from' => 'client',
                'status' => 'pending'

            ]);

            // Gửi thông báo
            $dataNotification = [
                'title' => 'Update Order',
                'message' => "Đơn hàng {$order->code} đã yêu cầu hủy. Vui lòng kiểm tra!",
                'from_user_id' => $order->id_user,
                'to_user_id' => null,
                'type' => 'orders',
                'status' => 'unread',
                'goto_id' => $order->id,
            ];
            $this->notificationService->sendPrivate($dataNotification);

            return $order;
        } catch (\Exception $e) {
            Log::error('Lỗi trong cancelOrder: ' . $e->getMessage());
            throw $e;
        }
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
    public function retryPayment($orderId)
    {
        try {
            if (!$this->canRetryPayment($orderId)) {
                throw new \Exception('Đơn hàng không đủ điều kiện để thanh toán lại.');
            }

            $order = $this->order->findOrFail($orderId);

            // Gọi API thanh toán (giả định PaymentService xử lý)
            $paymentResult = $this->paymentService->vnpay_payment($order);

            if ($paymentResult['success']) {
                $order->payment_status = 'Paid';
                $order->save();
                return true;
            } else {
                $order->payment_status = 'Payment Failed';
                $order->save();
                throw new \Exception('Thanh toán thất bại: ' . $paymentResult['message']);
            }
        } catch (\Exception $e) {
            Log::error('Lỗi trong retryPayment: ' . $e->getMessage());
            throw $e;
        }
    }
    public function canRetryPayment($orderId)
    {
        try {
            $order = $this->order->findOrFail($orderId);

            // Kiểm tra phương thức thanh toán
            if ($order->payment_method !== 'VNPAY') {
                return false;
            }

            // Kiểm tra trạng thái thanh toán
            $eligibleStatuses = ['Payment Failed', 'Payment Expired'];
            if (!in_array($order->payment_status, $eligibleStatuses)) {
                return false;
            }

            // Kiểm tra thời gian: đơn hàng phải trong vòng 12 tiếng
            $timeLimit = Carbon::parse($order->created_at)->addHours(12);
            if (Carbon::now()->greaterThan($timeLimit)) {
                return false;
            }

            // Kiểm tra tồn kho sản phẩm
            foreach ($order->orderDetails as $detail) {
                $variant = Product_variant::find($detail->id_variant);
                if (!$variant || $variant->stock < $detail->quantity) {
                    return false;
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Lỗi trong canRetryPayment: ' . $e->getMessage());
            return false;
        }
    }
    public function searchOrders(Request $request, $perPage = 10)
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

        // 2. Lọc theo nhóm trạng thái (group_status)
        if ($request->filled('group_status') && $request->group_status !== 'All') {
            $query->whereHas('orderStatus', function ($q) use ($request) {
                $q->where('group_status', $request->group_status);
            });
        }

        // 3. Lọc theo tổng tiền (min_total và max_total)
        if ($request->filled('min_total') && is_numeric($request->min_total)) {
            $query->where('total', '>=', $request->min_total);
        }
        if ($request->filled('max_total') && is_numeric($request->max_total)) {
            $query->where('total', '<=', $request->max_total);
        }

        // 4. Lọc theo phí vận chuyển (min_shipping và max_shipping)
        if ($request->filled('min_shipping') && is_numeric($request->min_shipping)) {
            $query->where('shipping', '>=', $request->min_shipping);
        }
        if ($request->filled('max_shipping') && is_numeric($request->max_shipping)) {
            $query->where('shipping', '<=', $request->max_shipping);
        }

        // 5. Sắp xếp (group_by)
        if ($request->filled('group_by')) {
            $query->orderBy($request->group_by);
        }

        // 6. Tính toán các chỉ số tổng hợp (summary metrics)
        $totalOrders = Order::where('id_user', $user->id)->count();

        // Tính openOrders: Tổng các đơn hàng thuộc Awaiting Delivery và Shipping
        $openOrders = Order::where('id_user', $user->id)
            ->whereHas('orderStatus', function ($q) {
                $q->whereIn('group_status', ['Awaiting Delivery', 'Shipping']);
            })->count();

        $averagePrice = Order::where('id_user', $user->id)->avg('total');

        // 7. Phân trang kết quả
        $orders = $query->orderBy('created_at', 'desc')->paginate($perPage);

        // 8. Lấy danh sách group_status duy nhất
        $groupStatuses = Order_status::select('group_status')
            ->distinct()
            ->whereNotNull('group_status')
            ->pluck('group_status')
            ->toArray();

        // 9. Tính số lượng đơn hàng cho mỗi group_status (dùng cho tabs)
        $groupStatusCounts = [];
        foreach ($groupStatuses as $group) {
            $groupStatusCounts[$group] = Order::where('id_user', $user->id)
                ->whereHas('orderStatus', function ($q) use ($group) {
                    $q->where('group_status', $group);
                })->count();
        }

        // Trả về dữ liệu
        return compact(
            'orders',
            'totalOrders',
            'openOrders',
            'averagePrice',
            'groupStatuses',
            'groupStatusCounts'
        );
    }


    function listReason()
    {
        return $this->orderCancellationReason->query()->select('id', 'reason')->where('to', 'client')->orderBy('id', 'DESC')->get();
    }
}
