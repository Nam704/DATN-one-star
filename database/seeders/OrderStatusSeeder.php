<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderStatusSeeder extends Seeder
{
    public function run()
    {
        $statuses = [
            'Pending',
            'Processing',
            'Shipped',
            'Delivered',
            'Cancelled'
        ];

        foreach($statuses as $status) {
            DB::table('order_statuses')->insert([
                'name' => $status,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
