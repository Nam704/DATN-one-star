<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                'password' => '12345678',
                'id_role' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'employee',
                'email' => 'employee@gmail.com',
                'password' => '12345678',
                'id_role' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],


        ]);
    }
}
