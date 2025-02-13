<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $brandIds = DB::table('brands')->pluck('id')->toArray();
        $categoryIds = DB::table('categories')->pluck('id')->toArray();

        foreach(range(1, 20) as $index) {
            DB::table('products')->insert([
                'name' => $faker->words(3, true),
                'id_brand' => $faker->randomElement($brandIds),
                'id_category' => $faker->randomElement($categoryIds),
                'description' => $faker->paragraph,
                'image_primary' => 'products/default.jpg',
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder products đã tạo thành công 20 bản ghi!');
    }
}
