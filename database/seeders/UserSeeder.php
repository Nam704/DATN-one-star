<?php

namespace Database\Seeders;

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
    }
}
