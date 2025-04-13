<?php

namespace Database\Seeders;

use App\Models\OrderCancellationReason;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderCancellationReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $reasons = [
            // Lý do hủy đơn cho client
            [
                'reason' => 'Thay đổi ý định, không muốn mua nữa',
                'to' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Tìm được sản phẩm khác phù hợp hơn',
                'to' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Giá cả không phù hợp',
                'to' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Thời gian giao hàng quá lâu',
                'to' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Sản phẩm không đúng như mô tả',
                'to' => 'client',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Lý do hủy đơn cho admin
            [
                'reason' => 'Hết hàng trong kho',
                'to' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Không thể giao hàng đến địa chỉ khách hàng',
                'to' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Đơn hàng vi phạm chính sách bán hàng',
                'to' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Khách hàng không xác nhận đơn hàng',
                'to' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'reason' => 'Lỗi hệ thống xử lý đơn hàng',
                'to' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('order_cancellation_reasons')->insert($reasons);
    }
}
