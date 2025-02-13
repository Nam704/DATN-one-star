<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ImportSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $supplierIds = DB::table('suppliers')->pluck('id')->toArray();

        foreach(range(1, 20) as $index) {
            DB::table('imports')->insert([
                'id_supplier' => $faker->randomElement($supplierIds),
                'import_date' => $faker->dateTimeBetween('-3 months', 'now'),
                'total_amount' => $faker->randomFloat(2, 1000, 10000),
                'note' => $faker->sentence,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder imports đã tạo thành công 20 bản ghi!');
    }
}
