<?php

declare(strict_types=1);

namespace App\Services\Calendly;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Low-level HTTP client for the Calendly API.
 */
class CalendlyClient
{
    /**
     * Get event details from Calendly.
     *
     * @param  string  $eventUri  The full URI of the scheduled event
     *
     * @throws ConnectionException
     */
    public function getEvent(string $eventUri): Response
    {
        return Http::withHeaders($this->headers())
            ->get($eventUri);
    }

    /**
     * @return array<string, string>
     */
    private function headers(): array
    {
        return [
            'Authorization' => 'Bearer '.config('services.calendly.pat'),
            'Content-Type' => 'application/json',
        ];
    }
}
