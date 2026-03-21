<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Jobs\ProcessCalendlyWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Handles incoming Calendly webhook requests.
 */
class CalendlyWebhookController extends Controller
{
    /**
     * Handle the incoming Calendly webhook.
     */
    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->verifySignature($request)) {
            Log::warning('Calendly webhook signature verification failed');

            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $event = $request->input('event');

        if ($event === 'invitee.created') {
            ProcessCalendlyWebhook::dispatch($request->input('payload'));
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Verify the Calendly webhook signature using HMAC-SHA256.
     */
    private function verifySignature(Request $request): bool
    {
        $signingKey = config('services.calendly.webhook_signing_key');

        if ($signingKey === '' || $signingKey === null) {
            Log::warning('Calendly webhook signing key not configured');

            return false;
        }

        $signatureHeader = $request->header('Calendly-Webhook-Signature', '');

        if ($signatureHeader === '') {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = explode('=', $part, 2);
            $parts[$key] = $value;
        }

        $timestamp = $parts['t'] ?? '';
        $signature = $parts['v1'] ?? '';

        if ($timestamp === '' || $signature === '') {
            return false;
        }

        $payload = $timestamp.'.'.$request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $signingKey);

        return hash_equals($expectedSignature, $signature);
    }
}
