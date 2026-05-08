<?php

declare(strict_types=1);

use App\Rules\NotFakeEmail;

uses(Tests\TestCase::class);

beforeEach(function () {
    config(['lead-validation.blocklist' => [
        'test', 'prova', 'fake', 'asd', 'foo', 'bar', 'example',
    ]]);
});

function runRule(string $email): ?string
{
    $message = null;
    (new NotFakeEmail)->validate('email', $email, function (string $msg) use (&$message) {
        $message = $msg;
    });

    return $message;
}

test('blocks fake host segment', function (string $email) {
    expect(runRule($email))->toBe('Per favore, inserisci un indirizzo email reale.');
})->with([
    'test.it' => 'info@test.it',
    'test.ai' => 'info@test.ai',
    'test.com' => 'info@test.com',
    'subdomain of test' => 'info@mail.test.com',
    'fake.io' => 'lead@fake.io',
    'example.com' => 'info@example.com',
]);

test('blocks fake local part', function (string $email) {
    expect(runRule($email))->toBe('Per favore, inserisci un indirizzo email reale.');
})->with([
    'test@gmail.com',
    'prova@gmail.com',
    'asd@gmail.com',
    'foo@yahoo.it',
    'mario.test@gmail.com',
    'mario_test@gmail.com',
    'mario+test@gmail.com',
    'mario-test@gmail.com',
    'test.user@gmail.com',
]);

test('is case-insensitive', function () {
    expect(runRule('TEST@TEST.IT'))->not->toBeNull();
    expect(runRule('Prova@Gmail.com'))->not->toBeNull();
});

test('accepts realistic addresses', function (string $email) {
    expect(runRule($email))->toBeNull();
})->with([
    'mario.rossi@gmail.com',
    'anna.bianchi@yahoo.it',
    'latest@gmail.com',
    'protest@gmail.com',
    'besttest@gmail.com',
    'ga4test@gmail.com',
    'tester@gmail.com',
    'foord@gmail.com',
    'luca@coine.it',
]);

test('skips non-string and malformed values', function () {
    expect(runRule(''))->toBeNull();
    expect(runRule('not-an-email'))->toBeNull();
});

test('reads blocklist from config at runtime', function () {
    config(['lead-validation.blocklist' => ['marvin']]);

    expect(runRule('hello@marvin.io'))->not->toBeNull();
    expect(runRule('test@gmail.com'))->toBeNull();
});
