<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slide;
use App\Models\SlideImage;

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
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            UserSeeder::class,
            AddressSeeder::class,
            OrderStatusSeeder::class,
            OrderCancellationReasonSeeder::class
        ]);

        // Tạo 10 slide
        Slide::factory(10)->create()->each(function ($slide) {
            // Mỗi slide có 3-5 ảnh
            SlideImage::factory(rand(3, 5))->create([
                'slide_id' => $slide->id,
            ]);
        });
    }
}
