<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['admin', 'employee', 'user'];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'name' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder roles đã tạo thành công!');
    }
}
