<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NotFakeEmail implements ValidationRule
{
    /**
     * Reject emails whose host or local part contains a segment listed in
     * the `lead-validation.blocklist` config (case-insensitive, exact on
     * the segment — not substring).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! str_contains($value, '@')) {
            return;
        }

        [$local, $host] = explode('@', strtolower($value), 2);

        $hostSegments = explode('.', $host);
        $localSegments = preg_split('/[._+\-]/', $local) ?: [];

        $blocklist = array_map('strtolower', (array) config('lead-validation.blocklist', []));

        if (array_intersect([...$hostSegments, ...$localSegments], $blocklist) !== []) {
            $fail('Per favore, inserisci un indirizzo email reale.');
        }
    }
}
