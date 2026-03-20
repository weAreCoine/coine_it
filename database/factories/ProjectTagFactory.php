<?php

namespace Database\Factories;

use App\Models\ProjectTag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectTag>
 */
class ProjectTagFactory extends Factory
{
    protected $model = ProjectTag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'slug' => fn (array $attributes) => \Illuminate\Support\Str::slug($attributes['name']),
            'description' => fake()->sentence(),
        ];
    }
}
