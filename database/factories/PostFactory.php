<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(3),
            'content' => fake()->paragraph(2),
            'cover' => fake()->imageUrl(),
            'user_id' => fake()->randomDigitNotNull(),
            'padlet_id' => fake()->randomDigitNotNull(),
        ];
    }
}
