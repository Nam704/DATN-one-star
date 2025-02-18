<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AttributeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $attributes = [
            'Size' => 'Product sizes',
            'Color' => 'Product colors',
            'Material' => 'Product materials',
            'Style' => 'Product styles',
            'Weight' => 'Product weights'
        ];

        foreach($attributes as $name => $description) {
            DB::table('attributes')->insert([
                'name' => $name,
                'description' => $description,
                'status' => $faker->randomElement(['active', 'inactive']),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        $this->command->info('✅ Seeder attributes đã tạo thành công!');
    }
}
