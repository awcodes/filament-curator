<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Database\Factories;

use Awcodes\Curator\Tests\Fixtures\Models\Author;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Author>
 */
class AuthorFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'twitter' => fake()->optional()->userName(),
            'bio' => fake()->optional()->paragraph(),
        ];
    }
}
