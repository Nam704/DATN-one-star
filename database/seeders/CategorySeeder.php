<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create parent categories first
        for($i = 1; $i <= 5; $i++) {
            DB::table('categories')->insert([
                'name' => $faker->unique()->word . ' Category',
                'id_parent' => null,
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        // Create child categories
        $parentIds = DB::table('categories')->pluck('id')->toArray();
        for($i = 1; $i <= 10; $i++) {
            DB::table('categories')->insert([
                'name' => $faker->unique()->word . ' Subcategory',
                'id_parent' => $faker->randomElement($parentIds),
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        $this->command->info('✅ Seeder categories đã tạo thành công 15 bản ghi!');
    }
}
