<?php

use App\Services\Meta\CapiClient;
use Combindma\FacebookPixel\Facades\MetaPixel;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\EventResponse;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Log;

uses(Tests\TestCase::class);

test('logs a structured info entry when the SDK returns a successful response', function () {
    $response = (new EventResponse)
        ->setEventsReceived(1)
        ->setFbTraceId('TRACE-1')
        ->setMessages([]);

    MetaPixel::shouldReceive('send')
        ->once()
        ->andReturn($response);

    $logger = Mockery::mock(\Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('info')
        ->once()
        ->withArgs(function (string $message, array $context) {
            return $message === 'Meta CAPI event sent'
                && $context['event_name'] === 'Lead'
                && $context['event_id'] === 'evt-1'
                && $context['events_received'] === 1
                && $context['fbtrace_id'] === 'TRACE-1'
                && is_int($context['duration_ms']);
        });

    Log::shouldReceive('channel')->with('meta-capi')->andReturn($logger);

    $result = (new CapiClient)->send('Lead', 'evt-1', new CustomData, new UserData);

    expect($result)->toBe($response);
});

test('logs an error entry and rethrows when the SDK throws', function () {
    MetaPixel::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('boom'));

    $logger = Mockery::mock(\Psr\Log\LoggerInterface::class);
    $logger->shouldReceive('error')
        ->once()
        ->withArgs(function (string $message, array $context) {
            return $message === 'Meta CAPI event failed'
                && $context['event_name'] === 'Lead'
                && $context['error'] === 'boom';
        });

    Log::shouldReceive('channel')->with('meta-capi')->andReturn($logger);

    expect(fn () => (new CapiClient)->send('Lead', 'evt-2', new CustomData, new UserData))
        ->toThrow(RuntimeException::class, 'boom');
});
