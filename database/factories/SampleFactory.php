<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sample>
 */
class SampleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = Str::title(fake()->unique()->words(2, true));

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => Str::camel($name),
            'status' => fake()->randomElement(['draft', 'active', 'archived']),
            'price' => fake()->randomFloat(2, 5, 500),
            'quantity' => fake()->numberBetween(0, 50),
            'color' => fake()->randomElement(['#1abc9c', '#3498db', '#e74c3c']),
            'is_featured' => fake()->boolean(),
        ];
    }

    /**
     * featured marks the sample as featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }
}
