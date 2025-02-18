<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('1234'),
                'id_role' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'employee1',
                'email' => 'employee@gmail.com',
                'password' => Hash::make('1234'),
                'id_role' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
=======
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
>>>>>>> 41311ec196c676571d9d1b179eb17a190b2f9c31
    }
}
