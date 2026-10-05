<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SiteStatKey;
use App\Models\SiteStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteStat>
 */
final class SiteStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => SiteStatKey::DeployCount,
            'value' => fake()->numberBetween(1, 500),
        ];
    }
}
