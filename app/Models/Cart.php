<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['id_user'];

    // Liên kết với bảng Users (1 giỏ hàng thuộc về 1 user)
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Liên kết với bảng CartItems (1 giỏ hàng có nhiều sản phẩm)
    public function items()
    {
        return $this->hasMany(CartItem::class, 'id_cart');
    }

    // Hàm tính tổng giá trị đơn hàng trước khi áp dụng giảm giá
    public function getSubtotal()
    {
        return $this->items->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }

    // Hàm tính tổng giá trị đơn hàng (có thể bao gồm giảm giá trong tương lai)
    public function getTotal()
    {
        return $this->getSubtotal(); // Nếu có giảm giá, bạn có thể sửa đổi logic tại đây
    }
}

