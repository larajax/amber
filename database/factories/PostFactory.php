<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->unique()->words(4, true));

        return [
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(16),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'status' => 'draft',
            'featured_color' => fake()->hexColor(),
            'views' => fake()->numberBetween(0, 5000),
            'published' => false,
        ];
    }

    /**
     * published marks the post as published at a time in the past.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'published' => true,
            'published_at' => fake()->dateTimeBetween('-6 months', '-1 hour'),
        ]);
    }
}
