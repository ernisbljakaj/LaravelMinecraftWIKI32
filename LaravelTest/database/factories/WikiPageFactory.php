<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WikiPage>
 */
class WikiPageFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['Redstone', 'Farmen', 'Bauen', 'Enchanting', 'Biome', 'Mobs', 'General'];

        return [
            'title' => fake()->unique()->sentence(4),
            'slug' => fn (array $attributes) => Str::slug($attributes['title']),
            'category' => fake()->randomElement($categories),
            'content' => implode("\n\n", fake()->paragraphs(5)),
            'excerpt' => fake()->sentence(12),
            'approved' => fake()->boolean(70),
            'user_id' => User::factory(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['approved' => true]);
    }
}
