<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductVariantAttributeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $variantIds = DB::table('product_variants')->pluck('id')->toArray();
        $attributeValueIds = DB::table('attribute_values')->pluck('id')->toArray();

        foreach(range(1, 50) as $index) {
            DB::table('product_variant_attributes')->insert([
                'id_product_variant' => $faker->randomElement($variantIds),
                'id_attribute_value' => $faker->randomElement($attributeValueIds),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder product_variant_attributes đã tạo thành công 50 bản ghi!');
    }
}
