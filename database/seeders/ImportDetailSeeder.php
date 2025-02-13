<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ImportDetailSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $importIds = DB::table('imports')->pluck('id')->toArray();
        $variantIds = DB::table('product_variants')->pluck('id')->toArray();

        foreach(range(1, 100) as $index) {
            $quantity = $faker->numberBetween(5, 50);
            $pricePerUnit = $faker->randomFloat(2, 10, 200);
            $expectedPrice = $faker->randomFloat(2, $pricePerUnit * 1.1, $pricePerUnit * 1.5);
            $totalPrice = $quantity * $pricePerUnit;

            DB::table('import_details')->insert([
                'id_import' => $faker->randomElement($importIds),
                'id_product_variant' => $faker->randomElement($variantIds),
                'quantity' => $quantity,
                'price_per_unit' => $pricePerUnit,
                'expected_price' => $expectedPrice,
                'total_price' => $totalPrice,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder import_details đã tạo thành công 100 bản ghi!');
    }
}
