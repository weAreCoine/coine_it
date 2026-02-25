<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
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
            'cover' => 'https://placehold.co/1200x630?font=roboto',
            'user_id' => \App\Models\User::factory(),
            'seo_title' => fake()->optional()->sentence(),
            'seo_description' => fake()->optional()->text(160),
            'seo_image' => 'https://placehold.co/1200x630?font=roboto',
            'is_published' => fake()->boolean(80),
            'is_featured' => fake()->boolean(20),
        ];
    }
}
