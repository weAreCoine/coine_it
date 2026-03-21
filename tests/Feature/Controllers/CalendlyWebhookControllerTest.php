<?php

declare(strict_types=1);

use App\Jobs\ProcessCalendlyWebhook;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    config([
        'services.calendly.webhook_signing_key' => 'test-signing-key',
    ]);
});

it('returns 403 when signature is missing', function () {
    $this->postJson('/api/webhooks/calendly', ['event' => 'invitee.created'])
        ->assertStatus(403);
});

it('returns 403 when signature is invalid', function () {
    $this->postJson('/api/webhooks/calendly', ['event' => 'invitee.created'], [
        'Calendly-Webhook-Signature' => 't=1234567890,v1=invalidsignature',
    ])->assertStatus(403);
});

it('returns 200 and dispatches job for valid invitee.created event', function () {
    Queue::fake();

    $payload = [
        'email' => 'test@example.com',
        'name' => 'John Doe',
        'cancel_url' => 'https://calendly.com/cancellations/abc',
        'reschedule_url' => 'https://calendly.com/reschedulings/abc',
        'scheduled_event' => [
            'uri' => 'https://api.calendly.com/scheduled_events/event-123',
            'start_time' => '2026-04-01T10:00:00.000000Z',
        ],
    ];

    $body = json_encode(['event' => 'invitee.created', 'payload' => $payload]);
    $timestamp = (string) time();
    $signature = hash_hmac('sha256', $timestamp.'.'.$body, 'test-signing-key');

    $this->call('POST', '/api/webhooks/calendly', [], [], [], [
        'HTTP_Calendly-Webhook-Signature' => "t={$timestamp},v1={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $body)->assertStatus(200);

    Queue::assertPushed(ProcessCalendlyWebhook::class, function ($job) {
        return $job->payload['email'] === 'test@example.com';
    });
});

it('returns 200 but does not dispatch job for unhandled events', function () {
    Queue::fake();

    $body = json_encode(['event' => 'invitee.canceled', 'payload' => []]);
    $timestamp = (string) time();
    $signature = hash_hmac('sha256', $timestamp.'.'.$body, 'test-signing-key');

    $this->call('POST', '/api/webhooks/calendly', [], [], [], [
        'HTTP_Calendly-Webhook-Signature' => "t={$timestamp},v1={$signature}",
        'CONTENT_TYPE' => 'application/json',
    ], $body)->assertStatus(200);

    Queue::assertNotPushed(ProcessCalendlyWebhook::class);
});

it('returns 403 when signing key is not configured', function () {
    config(['services.calendly.webhook_signing_key' => '']);

    $this->postJson('/api/webhooks/calendly', ['event' => 'invitee.created'], [
        'Calendly-Webhook-Signature' => 't=1234567890,v1=somesignature',
    ])->assertStatus(403);
});
