<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $userIds = DB::table('users')->pluck('id')->toArray();
        $statusIds = DB::table('order_statuses')->pluck('id')->toArray();
        $voucherIds = DB::table('vouchers')->pluck('id')->toArray();

        foreach(range(1, 20) as $index) {
            DB::table('orders')->insert([
                'id_user' => $faker->randomElement($userIds),
                'phone_number' => $faker->phoneNumber,
                'address' => $faker->address,
                'total_amount' => $faker->randomFloat(2, 100, 1000),
                'id_order_status' => $faker->randomElement($statusIds),
                'id_voucher' => $faker->optional(0.3)->randomElement($voucherIds),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
