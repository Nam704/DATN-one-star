<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ImageSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $variantIds = DB::table('product_variants')->pluck('id')->toArray();

        if (empty($variantIds)) {
            $this->command->warn('⚠️ Không có product_variants nào trong database. Seeder sẽ không chạy.');
            return;
        }

        foreach (range(1, 50) as $index) {
            DB::table('images')->insert([
                'url' => 'https://picsum.photos/seed/' . $faker->unique()->word . '/400/400',
                'id_product_variant' => $faker->randomElement($variantIds),
                'status' => $faker->boolean() ? 'active' : 'inactive',
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        $this->command->info('✅ Seeder images đã tạo thành công 50 bản ghi với ảnh từ Picsum!');
    }
}
