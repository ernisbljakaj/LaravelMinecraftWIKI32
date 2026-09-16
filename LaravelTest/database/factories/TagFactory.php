<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        $names = [
            'Survival', 'PvP', 'Creative', 'Skyblock', 'Bedwars',
            'Minigames', 'Anarchy', 'Vanilla', 'Modded', 'Hardcore',
        ];

        $name = fake()->unique()->randomElement($names);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'color' => fake()->hexColor(),
        ];
    }
}
