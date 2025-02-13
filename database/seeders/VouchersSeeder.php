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

        foreach(range(1, 10) as $index) {
            DB::table('vouchers')->insert([
                'name' => $faker->words(2, true) . ' Voucher',
                'code' => $faker->unique()->regexify('[A-Z]{3}[0-9]{3}'),
                'description' => $faker->sentence(),
                'discount_amount' => $faker->randomFloat(2, 10, 100),
                'quantity' => $faker->numberBetween(10, 100),
                'start_date' => $faker->dateTimeBetween('now', '+1 week'),
                'end_date' => $faker->dateTimeBetween('+1 week', '+1 month'),
                'min_amount' => $faker->randomFloat(2, 100, 500),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
