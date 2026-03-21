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
            'newsletter_opt_in' => false,
        ];
    }

    /**
     * State for a health check quiz lead with realistic quiz answers and score.
     */
    public function withHealthCheck(): static
    {
        return $this->state(function (array $attributes) {
            $answers = [
                'platform' => ['value' => fake()->randomElement(['woocommerce', 'shopify', 'prestashop_magento', 'custom']), 'points' => 0],
                'advertising' => ['value' => fake()->randomElement(['none', 'internal', 'freelance', 'agency']), 'points' => fake()->randomElement([3, 6, 9, 12])],
                'coordination' => ['value' => fake()->randomElement(['separate', 'external', 'self', 'internal']), 'points' => fake()->randomElement([0, 7, 15, 25])],
                'tracking' => ['value' => fake()->randomElement(['none', 'basic', 'decent', 'complete']), 'points' => fake()->randomElement([0, 8, 17, 25])],
                'mobile' => ['value' => fake()->randomElement(['unknown', 'slow', 'ok', 'optimized']), 'points' => fake()->randomElement([0, 5, 13, 20])],
                'retention' => ['value' => fake()->randomElement(['none', 'basic', 'active', 'advanced']), 'points' => fake()->randomElement([0, 6, 14, 18])],
            ];

            $score = collect($answers)->except('platform')->sum('points');

            return [
                'website' => fake()->domainName(),
                'quiz_score' => $score,
                'quiz_answers' => $answers,
                'newsletter_opt_in' => true,
            ];
        });
    }
}
