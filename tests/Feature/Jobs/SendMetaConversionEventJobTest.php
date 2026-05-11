<?php

use App\Jobs\SendMetaConversionEventJob;
use App\Services\Meta\CapiClient;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\UserData;
use Illuminate\Support\Facades\Bus;

test('send() dispatches to the queue when queue_enabled is true', function () {
    Bus::fake();
    config()->set('meta-pixel.queue_enabled', true);

    $userData = (new UserData)
        ->setEmail('mario.rossi@example.com')
        ->setFirstName('mario')
        ->setLastName('rossi');

    SendMetaConversionEventJob::send('Lead', 'evt-1', $userData);

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        function (SendMetaConversionEventJob $job) {
            return $job->eventName === 'Lead'
                && $job->eventId === 'evt-1'
                && ($job->userDataAttributes['email'] ?? null) === 'mario.rossi@example.com'
                && ($job->userDataAttributes['firstName'] ?? null) === 'mario';
        },
    );
});

test('send() runs synchronously when queue_enabled is false', function () {
    config()->set('meta-pixel.queue_enabled', false);

    $client = Mockery::mock(CapiClient::class);
    $client->shouldReceive('send')
        ->once()
        ->withArgs(function (string $eventName, string $eventId, CustomData $custom, UserData $userData) {
            return $eventName === 'Lead'
                && $eventId === 'evt-2'
                && $userData->getEmail() === 'mario.rossi@example.com';
        });

    app()->instance(CapiClient::class, $client);

    SendMetaConversionEventJob::send(
        'Lead',
        'evt-2',
        (new UserData)->setEmail('mario.rossi@example.com'),
    );
});

test('handle() rebuilds UserData and CustomData from the serialized attributes and calls the CapiClient', function () {
    $captured = null;

    $client = Mockery::mock(CapiClient::class);
    $client->shouldReceive('send')
        ->once()
        ->withArgs(function (string $eventName, string $eventId, CustomData $custom, UserData $userData) use (&$captured) {
            $captured = ['custom' => $custom, 'user' => $userData];

            return $eventName === 'Lead' && $eventId === 'evt-3';
        });

    $job = new SendMetaConversionEventJob(
        eventName: 'Lead',
        eventId: 'evt-3',
        userDataAttributes: [
            'email' => 'lead@example.com',
            'phone' => '39123456789',
            'country' => 'it',
        ],
        customDataAttributes: [
            'value' => 42.5,
            'currency' => 'EUR',
        ],
    );

    $job->handle($client);

    expect($captured['user']->getEmail())->toBe('lead@example.com')
        ->and($captured['user']->getPhone())->toBe('39123456789')
        ->and($captured['user']->getCountryCode())->toBe('it')
        ->and((float) $captured['custom']->getValue())->toBe(42.5)
        ->and($captured['custom']->getCurrency())->toBe('EUR');
});
