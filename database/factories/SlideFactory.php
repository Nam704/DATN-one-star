<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Slide;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Slide>
 */
class SlideFactory extends Factory
{
    protected $model = Slide::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'category_id' => \App\Models\Category::inRandomOrder()->first()?->id, // Lấy id ngẫu nhiên từ categories
            'is_active' => $this->faker->boolean(),
            'display_locations' => json_encode(['home', 'banner']), // Giá trị mẫu
        ];
    }
}
