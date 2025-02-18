<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class VouchersSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 20) as $index) {
            $discountAmount = $faker->randomElement([50000, 100000, 150000, 200000, 300000]);
            $minAmount = $discountAmount * 5;
            
            DB::table('vouchers')->insert([
                'name' => 'Giảm ' . number_format($discountAmount) . 'đ',
                'code' => 'SALE' . $faker->unique()->numberBetween(100, 999),
                'description' => 'Giảm ' . number_format($discountAmount) . 'đ cho đơn hàng từ ' . number_format($minAmount) . 'đ',
                'discount_amount' => $discountAmount,
                'quantity' => $faker->numberBetween(10, 100),
                'start_date' => $faker->dateTimeBetween('now', '+1 week'),
                'end_date' => $faker->dateTimeBetween('+1 week', '+3 months'),
                'min_amount' => $minAmount,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
