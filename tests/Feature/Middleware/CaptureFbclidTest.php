<?php

use App\Http\Middleware\CaptureFbclid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

function fbclidRequest(string $url, array $cookies = []): Request
{
    $request = Request::create($url, 'GET', [], $cookies);
    $request->setLaravelSession(app('session.store'));

    app()->instance('request', $request);

    return $request;
}

function runFbclidMiddleware(Request $request): Response
{
    return app(CaptureFbclid::class)->handle($request, fn () => new Response('ok'));
}

function queuedFbcCookie(): ?Symfony\Component\HttpFoundation\Cookie
{
    foreach (Cookie::getQueuedCookies() as $cookie) {
        if ($cookie->getName() === '_fbc' && $cookie->getValue() !== null && $cookie->getValue() !== '') {
            return $cookie;
        }
    }

    return null;
}

test('parks the incoming fbclid in the session without emitting the _fbc cookie when marketing consent is missing', function () {
    $request = fbclidRequest('https://coine.test/?fbclid=ABC123');

    $response = runFbclidMiddleware($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($request->session()->get('meta.fbclid'))->toBe('ABC123')
        ->and($request->session()->get('meta.fbclid_ts'))->toBeInt()
        ->and(queuedFbcCookie())->toBeNull();
});

test('emits the _fbc cookie post-consent built from a freshly captured fbclid', function () {
    $request = fbclidRequest('https://coine.test/?fbclid=ABC123', [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);

    runFbclidMiddleware($request);

    $queued = queuedFbcCookie();

    expect($queued)->not->toBeNull()
        ->and($queued->getValue())->toMatch('/^fb\.1\.\d+\.ABC123$/');
});

test('rebuilds the _fbc cookie from a previously parked fbclid when consent arrives later', function () {
    $first = fbclidRequest('https://coine.test/?fbclid=ABC123');
    runFbclidMiddleware($first);

    $second = fbclidRequest('https://coine.test/about', [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);
    $second->setLaravelSession($first->session());
    app()->instance('request', $second);

    runFbclidMiddleware($second);

    $queued = queuedFbcCookie();

    expect($queued)->not->toBeNull()
        ->and($queued->getValue())->toMatch('/^fb\.1\.\d+\.ABC123$/');
});

test('does not overwrite an existing _fbc cookie even when fbclid is in session', function () {
    $request = fbclidRequest('https://coine.test/?fbclid=ABC123', [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
        '_fbc' => 'fb.1.111.PREVIOUS',
    ]);

    runFbclidMiddleware($request);

    expect(queuedFbcCookie())->toBeNull();
});

test('does nothing when neither fbclid query param nor session payload is present', function () {
    $request = fbclidRequest('https://coine.test/', [
        'cookie_consent' => json_encode(['necessary' => true, 'marketing' => true]),
    ]);

    runFbclidMiddleware($request);

    expect(queuedFbcCookie())->toBeNull()
        ->and($request->session()->has('meta.fbclid'))->toBeFalse();
});
