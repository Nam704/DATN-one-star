<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ProductAuditSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        
        $userIds = DB::table('users')->pluck('id')->toArray();
        $variantIds = DB::table('product_variants')->pluck('id')->toArray();

        foreach(range(1, 50) as $index) {
            DB::table('product_audits')->insert([
                'id_user' => $faker->randomElement($userIds),
                'id_product_variant' => $faker->randomElement($variantIds),
                'quantity' => $faker->numberBetween(-10, 10),
                'action_type' => $faker->randomElement(['import', 'export', 'adjust']),
                'reason' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder product_audits đã tạo thành công 50 bản ghi!');
    }
}
