<?php

namespace Database\Factories;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'project' => fake()->optional()->paragraph(),
            'terms' => true,
            'services' => null,
            'budget' => null,
        ];
    }

    /**
     * State for a health check quiz lead with realistic quiz answers and score.
     */
    public function withHealthCheck(): static
    {
        return $this->state(fn (array $attributes) => [
            'website' => fake()->domainName(),
            'quiz_score' => fake()->numberBetween(20, 100),
            'quiz_answers' => [
                'platform' => fake()->randomElement(['WordPress', 'Shopify', 'Custom', 'Wix', 'Squarespace']),
                'advertising' => fake()->randomElement(['Google Ads', 'Meta Ads', 'None', 'Both']),
                'seo' => fake()->randomElement(['Yes', 'No', 'Partially']),
                'analytics' => fake()->randomElement(['Google Analytics', 'None', 'Other']),
                'social_media' => fake()->randomElement(['Instagram', 'Facebook', 'LinkedIn', 'None']),
                'goals' => fake()->randomElement(['More traffic', 'More leads', 'Brand awareness', 'E-commerce sales']),
            ],
        ]);
    }
}
