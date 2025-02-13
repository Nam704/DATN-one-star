<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductVariantSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $productIds = DB::table('products')->pluck('id')->toArray();

        foreach(range(1, 30) as $index) {
            DB::table('product_variants')->insert([
                'id_product' => $faker->randomElement($productIds),
                'sku' => $faker->unique()->ean13,
                'status' => $faker->randomElement(['active', 'inactive']),
                'quantity' => $faker->numberBetween(0, 1000),
                'price' => $faker->numberBetween(100000, 10000000),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        $this->command->info('✅ Seeder product_variants đã tạo thành công 30 bản ghi!');
    }
}
