<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class AttributeValueSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $attributeIds = DB::table('attributes')->pluck('id')->toArray();

        foreach($attributeIds as $attributeId) {
            foreach(range(1, 5) as $index) {
                DB::table('attribute_values')->insert([
                    'id_attribute' => $attributeId,
                    'value' => $faker->word,
                    'status' => $faker->randomElement(['active', 'inactive']),
                    'created_at' => now(),
                    'updated_at' => now(),
                    'deleted_at' => null
                ]);
            }
        }

        $this->command->info('✅ Seeder attribute_values đã tạo thành công!');
    }
}
