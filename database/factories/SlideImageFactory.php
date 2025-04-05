<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\SlideImage;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SlideImage>
 */
class SlideImageFactory extends Factory
{
    protected $model = SlideImage::class;

    public function definition()
    {
        return [
            'slide_id' => \App\Models\Slide::inRandomOrder()->first()?->id, // Lấy id slide ngẫu nhiên
            'image' => $this->faker->imageUrl(800, 400, 'slides'), // Ảnh giả
            'is_primary' => $this->faker->boolean(),
        ];
    }
}
