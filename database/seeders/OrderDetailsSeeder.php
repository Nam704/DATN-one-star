<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class OrderDetailsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $orderIds = DB::table('orders')->pluck('id')->toArray();
        $productVariantIds = DB::table('product_variants')->pluck('id')->toArray();

        foreach(range(1, 50) as $index) {
            $quantity = $faker->numberBetween(1, 5);
            $unitPrice = $faker->randomFloat(2, 10, 200);
            
            DB::table('order_details')->insert([
                'id_order' => $faker->randomElement($orderIds),
                'id_product_variant' => $faker->randomElement($productVariantIds),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
                'product_name' => $faker->words(3, true),
                'name_variant' => $faker->word,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
