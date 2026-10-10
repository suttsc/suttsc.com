<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\StravaActivity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StravaActivity>
 */
final class StravaActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'strava_id' => fake()->unique()->numberBetween(1, PHP_INT_MAX),
            'name' => fake()->words(3, true),
            'sport_type' => 'Run',
            'distance_metres' => fake()->numberBetween(3000, 21100),
            'moving_seconds' => fake()->numberBetween(900, 7200),
            'elevation_metres' => fake()->numberBetween(0, 250),
            'started_at' => fake()->dateTimeBetween('-6 days'),
        ];
    }
}
