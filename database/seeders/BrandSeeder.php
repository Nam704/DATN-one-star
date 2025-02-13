<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BrandSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach(range(1, 10) as $index) {
            DB::table('brands')->insert([
                'name' => $faker->company,
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        $this->command->info('✅ Seeder brands đã tạo thành công 10 bản ghi!');
    }
}
