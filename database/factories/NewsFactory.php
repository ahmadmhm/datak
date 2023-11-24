<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\News>
 */
class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->name(),
            'published_at' => now()->subDays(fake()->randomNumber(1))->addHours(fake()->randomNumber(1)),
            'source' => fake()->name(),
            'context' => fake()->sentence(),
            'link' => fake()->url(),
        ];
    }
}
