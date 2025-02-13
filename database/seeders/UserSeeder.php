<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $roleIds = DB::table('roles')->pluck('id')->toArray();

        foreach(range(1, 10) as $index) {
            DB::table('users')->insert([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'is_lock' => $faker->boolean(20), // 20% chance of being locked
                'status' => $faker->randomElement(['active', 'inactive']),
                'id_role' => $faker->randomElement($roleIds),
                'email_verified_at' => $faker->optional(0.8)->dateTimeThisYear, // 80% verified
                'password' => Hash::make('password123'),
                'profile_image' => 'users/default.jpg',
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null
            ]);
        }

        $this->command->info('✅ Seeder users đã tạo thành công 10 bản ghi!');
    }
}
