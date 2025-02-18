<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
<<<<<<< HEAD
        $this->call([
            RoleSeeder::class,
        ]);
=======
        
        $this->call([
            RoleSeeder::class,
        UserSeeder::class,
        ProductSeeder::class,
        ProductVariantSeeder::class,
        VouchersSeeder::class,
        OrderStatusSeeder::class,
        OrdersSeeder::class,
        OrderDetailsSeeder::class
        ]);

>>>>>>> 41311ec196c676571d9d1b179eb17a190b2f9c31
    }
}
