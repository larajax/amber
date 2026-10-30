<?php

namespace Database\Factories;

use App\Models\Sample;
use App\Models\SampleLink;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SampleLink>
 */
class SampleLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = Str::title(fake()->words(2, true));

        return [
            'sample_id' => Sample::factory(),
            'title' => $title,
            'url' => 'https://example.com/'.Str::slug($title),
        ];
    }
}
