<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();

        return [
            'title' => $title,
            'content' => fake()->paragraphs(5, true),
            'slug' => \Illuminate\Support\Str::slug($title).'-'.fake()->unique()->numberBetween(1, 10000),
            'cover' => null,
            'user_id' => \App\Models\User::factory(),
            'seo_title' => fake()->optional()->sentence(),
            'seo_description' => fake()->optional()->text(160),
            'seo_image' => null,
            'is_published' => fake()->boolean(80),
            'is_featured' => fake()->boolean(20),
            'goal' => fake()->optional()->sentence(),
            'tools' => fake()->optional()->words(5, true),
            'results' => fake()->optional()->sentence(),
        ];
    }
}
