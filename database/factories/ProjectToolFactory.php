<?php

namespace Database\Factories;

use App\Models\ProjectTool;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectTool>
 */
class ProjectToolFactory extends Factory
{
    protected $model = ProjectTool::class;

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
