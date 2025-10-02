<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShortUrl>
 */
class ShortUrlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'original_url' => $this->faker->url(),
            'short_code' => $this->faker->unique()->regexify('[A-Za-z0-9]{8}'),
            'clicks' => $this->faker->numberBetween(0, 1000),
            'expires_at' => $this->faker->optional(0.3)->dateTimeBetween('now', '+1 year'),
        ];
    }
}
