<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('order_statuses')->insert([
            ['name' => 'Pending', 'created_at' => now(), 'updated_at' => now()], // Chờ xác nhận
            ['name' => 'Processing', 'created_at' => now(), 'updated_at' => now()], // Đang xử lý
            ['name' => 'Shipping', 'created_at' => now(), 'updated_at' => now()], // Đang vận chuyển
            ['name' => 'Delivered', 'created_at' => now(), 'updated_at' => now()], // Đã giao hàng
            ['name' => 'Cancelled', 'created_at' => now(), 'updated_at' => now()], // Đã hủy
            ['name' => 'Refunded', 'created_at' => now(), 'updated_at' => now()], // Hoàn tiền
            ['name' => 'Failed Delivery', 'created_at' => now(), 'updated_at' => now()], // Giao hàng thất bại
            ['name' => 'Awaiting Payment', 'created_at' => now(), 'updated_at' => now()], // Chờ thanh toán
            ['name' => 'Paid', 'created_at' => now(), 'updated_at' => now()], // Đã thanh toán
            ['name' => 'Payment Verification', 'created_at' => now(), 'updated_at' => now()], // Đang xác minh thanh toán
        ]);
    }
}
