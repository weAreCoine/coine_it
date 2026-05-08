<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CookieConsent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CookieConsent>
 */
class CookieConsentFactory extends Factory
{
    protected $model = CookieConsent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $marketing = fake()->boolean();
        $analytics = fake()->boolean();

        return [
            'consent_id' => (string) Str::uuid(),
            'external_id' => (string) Str::uuid(),
            'necessary' => true,
            'marketing' => $marketing,
            'analytics' => $analytics,
            'choice_type' => $this->resolveChoiceType($marketing, $analytics),
            'ip_hash' => hash('sha256', fake()->ipv4().'salt'),
            'user_agent' => fake()->userAgent(),
            'referer' => fake()->optional()->url(),
            'path' => '/'.fake()->slug(),
            'version' => '1.0',
        ];
    }

    public function acceptAll(): static
    {
        return $this->state(fn () => [
            'marketing' => true,
            'analytics' => true,
            'choice_type' => 'accept_all',
        ]);
    }

    public function rejectAll(): static
    {
        return $this->state(fn () => [
            'marketing' => false,
            'analytics' => false,
            'choice_type' => 'reject_all',
        ]);
    }

    public function update(): static
    {
        return $this->state(fn () => [
            'choice_type' => 'update',
        ]);
    }

    private function resolveChoiceType(bool $marketing, bool $analytics): string
    {
        return match (true) {
            $marketing && $analytics => 'accept_all',
            ! $marketing && ! $analytics => 'reject_all',
            default => 'custom',
        };
    }
}
