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
        // Chèn các trạng thái vào bảng order_statuses
        DB::table('order_statuses')->insert([
            ['name' => 'Pending', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Không có trạng thái tiếp theo
            ['name' => 'Processing', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Chờ xử lý
            ['name' => 'Shipping', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Đang vận chuyển
            ['name' => 'Delivered', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Đã giao hàng
            ['name' => 'Cancelled', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Đã hủy
            ['name' => 'Refunded', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Hoàn tiền
            ['name' => 'Failed Delivery', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Giao hàng thất bại
            ['name' => 'Awaiting Payment', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Chờ thanh toán
            ['name' => 'Paid', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Đã thanh toán
            ['name' => 'Payment Verification', 'created_at' => now(), 'updated_at' => now(), 'next_status_id' => null], // Đang xác minh thanh toán
        ]);

        // Sau khi các trạng thái đã được chèn, chúng ta sẽ cập nhật `next_status_id` cho các trạng thái
        // Các trạng thái tiếp theo sẽ được cập nhật từ bảng `order_statuses`
        DB::table('order_statuses')->where('name', 'Pending')->update(['next_status_id' => DB::table('order_statuses')->where('name', 'Processing')->value('id')]);
        DB::table('order_statuses')->where('name', 'Processing')->update(['next_status_id' => DB::table('order_statuses')->where('name', 'Shipping')->value('id')]);
        DB::table('order_statuses')->where('name', 'Shipping')->update(['next_status_id' => DB::table('order_statuses')->where('name', 'Delivered')->value('id')]);
        DB::table('order_statuses')->where('name', 'Delivered')->update(['next_status_id' => null]);
        DB::table('order_statuses')->where('name', 'Cancelled')->update(['next_status_id' => null]);
        DB::table('order_statuses')->where('name', 'Refunded')->update(['next_status_id' => null]);
        DB::table('order_statuses')->where('name', 'Failed Delivery')->update(['next_status_id' => null]);
        DB::table('order_statuses')->where('name', 'Awaiting Payment')->update(['next_status_id' => DB::table('order_statuses')->where('name', 'Paid')->value('id')]);
        DB::table('order_statuses')->where('name', 'Paid')->update(['next_status_id' => DB::table('order_statuses')->where('name', 'Payment Verification')->value('id')]);
        DB::table('order_statuses')->where('name', 'Payment Verification')->update(['next_status_id' => null]);
    }
}
