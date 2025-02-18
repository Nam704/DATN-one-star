<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
=======
>>>>>>> 41311ec196c676571d9d1b179eb17a190b2f9c31
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
<<<<<<< HEAD
    /**
     * Run the database seeds.
     */
=======
>>>>>>> 41311ec196c676571d9d1b179eb17a190b2f9c31
    public function run()
    {
        $roles = ['admin', 'employee', 'user'];

        foreach ($roles as $role) {
<<<<<<< HEAD
            Role::factory()->create(['name' => $role]);
        }
=======
            DB::table('roles')->insert([
                'name' => $role,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('✅ Seeder roles đã tạo thành công!');
>>>>>>> 41311ec196c676571d9d1b179eb17a190b2f9c31
    }
}
