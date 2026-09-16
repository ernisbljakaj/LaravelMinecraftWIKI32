<?php

namespace Database\Factories;

use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Server>
 */
class ServerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'slug' => fn (array $attributes) => Str::slug($attributes['name']),
            'ip' => fake()->domainName(),
            'version' => fake()->randomElement(['1.20', '1.21', '1.21.1', '1.21.4']),
            'mode' => fake()->randomElement(['Survival', 'Creative', 'PvP', 'Skyblock', 'Bedwars', 'Minigames', 'Anarchy']),
            'description' => fake()->paragraph(3),
            'approved' => fake()->boolean(70),
            'featured' => fake()->boolean(10),
            'user_id' => User::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['approved' => true]);
    }

    public function unapproved(): static
    {
        return $this->state(fn () => ['approved' => false]);
    }

    public function notFeatured(): static
    {
        return $this->state(fn () => ['featured' => false]);
    }
}
