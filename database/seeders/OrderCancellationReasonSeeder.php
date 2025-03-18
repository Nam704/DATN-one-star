<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderCancellationReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        OrderCancellationReason::create(['reason' => 'Sản phẩm không còn sẵn có']);
        OrderCancellationReason::create(['reason' => 'Giao hàng chậm trễ']);
        OrderCancellationReason::create(['reason' => 'Lỗi khi đặt hàng']);
        OrderCancellationReason::create(['reason' => 'Không muốn mua nữa']);
    }
}
