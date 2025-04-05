<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        "code",
        "user_name",
        "id_user",
        "phone_number",
        "email",
        "id_address",
        "address_detail",
        "note",
        "subtotal",
        "shipping",
        "payment_method",
        "payment_status",
        "id_ward",
        "total",
        "id_order_status",
        "id_voucher",

    ];
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
    public function details()
    {
        return $this->load([
            "orderDetails" => function ($query) {
                $query->select("id", "id_order", "id_variant", "quantity", "unit_price", "total")
                    ->with([
                        "productVariant" => function ($query) {
                            $query->select('id', 'sku', 'id_product')
                                ->with([
                                    "product" => function ($query) {
                                        $query->select('id', 'name', 'image_primary', 'id_brand', 'id_category')
                                            ->with([
                                                "Category" => function ($query) {
                                                    $query->select('id', 'name');
                                                },
                                                "Brand" => function ($query) {
                                                    $query->select('id', 'name');
                                                }
                                            ]);
                                    },
                                    "images" => function ($query) {
                                        $query->select('id', 'id_product_variant', 'url');
                                    },
                                    "attributeValues"

                                ]);
                        }
                    ]);
            }
        ]);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
