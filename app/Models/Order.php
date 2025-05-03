<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'id_user',
        'user_data',
        'address_data',
        'voucher_data',
        'note',
        'subtotal',
        'shipping',
        'total',
        'payment_method',
        'payment_status',
        'id_order_status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'user_data' => 'array',
        'address_data' => 'array',
        'voucher_data' => 'array',
    ];
    public function orderCancellations()
    {
        return $this->hasMany(OrderCancellation::class, 'order_id');
    }
    public function refunds()
    {
        return $this->hasMany(Refund::class);
    }

    // Kiểm tra đơn có thể hoàn tiền
    public function isRefundable()
    {
        return in_array($this->id_order_status, [
            Order_status::where('name', 'Return Requested')->first()->id,
            Order_status::where('name', 'Return Approved')->first()->id
        ]);
    }
    public function orderExpire()
    {
        return $this->hasOne(OrderExpire::class, 'id_order', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'id_address');
    }
    // Quan hệ với bảng trạng thái đơn hàng
    public function orderStatus()
    {
        return $this->belongsTo(Order_status::class, 'id_order_status', 'id');
    }
    public function status()
    {
        return $this->belongsTo(Order_status::class, 'id_order_status', 'id');
    }

    // Quan hệ với bảng chi tiết đơn hàng
    public function orderDetails()
    {
        return $this->hasMany(Order_detail::class, 'id_order');
    }

    // Quan hệ với bảng voucher (nếu có)
    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'id_voucher');
    }

    public function getComputedTotalAttribute()
    {
        return $this->orderDetails->sum(function ($detail) {
            return $detail->quantity * $detail->unit_price;
        });
    }
    // Hàm details tối ưu hóa dữ liệu

    public function detailsOrder()
    {
        // Lấy thông tin đơn hàng cơ bản
        $orderData = $this->only([
            'id',
            'code',
            'created_at',
            'payment_method',
            'payment_status',
            'subtotal',
            'shipping',
            'total',
            'note'
        ]);

        // Lấy thông tin user từ user_data (JSON)
        $userData = json_decode($this->user_data, true);
        $orderData['user_name'] = $userData['name'] ?? 'N/A';
        $orderData['user_phone'] = $userData['phone'] ?? 'N/A';
        $orderData['user_email'] = $userData['email'] ?? 'N/A';

        // Lấy thông tin địa chỉ từ address_data (JSON)
        $addressData = json_decode($this->address_data, true);
        $orderData['address'] = $addressData['address_detail'] ?? 'N/A';
        $orderData['ward'] = $addressData['name_ward'] ?? 'N/A';
        $orderData['district'] = $addressData['name_district'] ?? 'N/A';
        $orderData['province'] = $addressData['name_province'] ?? 'N/A';

        // Lấy thông tin voucher từ voucher_data (JSON, nếu có)
        $voucherData = json_decode($this->voucher_data, true);
        $orderData['voucher_code'] = $voucherData['code'] ?? 'N/A';
        $orderData['discount'] = $voucherData['discount'] ?? 'N/A';

        // Lấy trạng thái đơn hàng từ quan hệ orderStatus
        $orderData['order_status'] = $this->orderStatus ? $this->orderStatus->only('id', 'name') : null;

        // Lấy chi tiết đơn hàng từ orderDetails và variant_data (JSON)
        $orderDetails = $this->orderDetails->map(function ($detail) {
            $variantData = json_decode($detail->variant_data, true);

            return [
                'id' => $detail->id,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'total' => $detail->total,
                'name' => $variantData['name'] ?? 'N/A',
                'sku' => $variantData['sku'] ?? 'N/A',
                'image' => $variantData['image'] ?? 'default.jpg',
                'attributes' => $variantData['values'] ?? [],
            ];
        });

        // Thêm chi tiết đơn hàng vào dữ liệu trả về
        $orderData['order_details'] = $orderDetails;

        return $orderData;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

}
