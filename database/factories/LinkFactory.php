<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\LinkCategory;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
final class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'url' => fake()->url(),
            'title' => fake()->sentence(),
            'author' => fake()->name(),
            'category' => fake()->randomElement(LinkCategory::cases()),
            'reason' => fake()->sentence(),
        ];
    }

    /**
     * A link whose title has not been fetched yet.
     */
    public function untitled(): self
    {
        return $this->state(['title' => null]);
    }
}
