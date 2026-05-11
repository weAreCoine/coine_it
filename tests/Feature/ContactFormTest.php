<?php

use App\Jobs\SendMetaConversionEventJob;
use App\Mail\LeadReceived;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john.doe@gmail.com',
        'phone' => '+39123456789',
        'message' => 'I need a website',
        'termsAccepted' => true,
        'metaEventId' => '11111111-2222-4333-8444-555555555555',
    ], $overrides);
}

test('creates a lead with correct field mapping', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload())
        ->assertRedirect();

    $this->assertDatabaseHas('leads', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'name' => 'John Doe',
        'email' => 'john.doe@gmail.com',
        'phone' => '+39123456789',
        'project' => 'I need a website',
        'terms' => true,
    ]);
});

test('accepts nullable phone', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'firstName' => 'Jane',
        'email' => 'jane.doe@gmail.com',
        'phone' => null,
        'message' => 'Help me',
    ]))->assertRedirect();

    $this->assertDatabaseHas('leads', [
        'name' => 'Jane Doe',
        'email' => 'jane.doe@gmail.com',
        'phone' => null,
    ]);
});

test('dispatches LeadCreated event and sends LeadReceived mailable', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'pippo.utente@gmail.com',
        'phone' => null,
        'message' => 'Project description',
    ]));

    Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) {
        return $mail->hasTo('luca@coine.it')
            && $mail->hasCc('silvia@coine.it');
    });
});

test('LeadReceived mailable has correct subject and from', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'mailable.user@gmail.com',
        'phone' => null,
        'message' => 'Check mailable',
    ]));

    Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) {
        return $mail->hasFrom('site@coine.it')
            && $mail->hasSubject('Un nuovo lead da coine.it');
    });
});

test('validation requires firstName', function () {
    $this->post(route('contact.store'), contactPayload(['firstName' => '']))
        ->assertSessionHasErrors('firstName');
});

test('validation requires lastName', function () {
    $this->post(route('contact.store'), contactPayload(['lastName' => '']))
        ->assertSessionHasErrors('lastName');
});

test('validation requires email', function () {
    $this->post(route('contact.store'), contactPayload(['email' => '']))
        ->assertSessionHasErrors('email');
});

test('validation requires valid email format', function () {
    $this->post(route('contact.store'), contactPayload(['email' => 'not-an-email']))
        ->assertSessionHasErrors('email');
});

test('validation requires message', function () {
    $this->post(route('contact.store'), contactPayload(['message' => '']))
        ->assertSessionHasErrors('message');
});

test('validation requires a uuid metaEventId', function () {
    $this->post(route('contact.store'), contactPayload(['metaEventId' => 'not-a-uuid']))
        ->assertSessionHasErrors('metaEventId');
});

test('validation requires metaEventId', function () {
    $this->post(route('contact.store'), contactPayload(['metaEventId' => '']))
        ->assertSessionHasErrors('metaEventId');
});

test('stores newsletter_opt_in as true when newsletterOptIn is true', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'email' => 'newsletter.user@gmail.com',
        'phone' => null,
        'message' => 'I want newsletter',
        'newsletterOptIn' => true,
    ]))->assertRedirect();

    $this->assertDatabaseHas('leads', [
        'email' => 'newsletter.user@gmail.com',
        'newsletter_opt_in' => true,
    ]);
});

test('stores newsletter_opt_in as false when newsletterOptIn is omitted', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'email' => 'nonews.user@gmail.com',
        'phone' => null,
        'message' => 'No newsletter',
    ]))->assertRedirect();

    $this->assertDatabaseHas('leads', [
        'email' => 'nonews.user@gmail.com',
        'newsletter_opt_in' => false,
    ]);
});

test('validation requires termsAccepted', function () {
    $this->post(route('contact.store'), contactPayload(['termsAccepted' => false]))
        ->assertSessionHasErrors('termsAccepted');
});

test('validation rejects disposable email', function () {
    $this->post(route('contact.store'), contactPayload(['email' => 'pippo@mailinator.com']))
        ->assertSessionHasErrors('email');
});

test('validation rejects fake host segment', function () {
    $this->post(route('contact.store'), contactPayload(['email' => 'info@test.it']))
        ->assertSessionHasErrors('email');
});

test('validation rejects fake local part on real domain', function () {
    $this->post(route('contact.store'), contactPayload(['email' => 'prova@gmail.com']))
        ->assertSessionHasErrors('email');
});

test('validation rejects fake local part with separators', function () {
    $this->post(route('contact.store'), contactPayload(['email' => 'mario.test@gmail.com']))
        ->assertSessionHasErrors('email');
});

test('forwards the browser-provided metaEventId 1:1 to the Meta CAPI Lead event', function () {
    Mail::fake();
    Bus::fake();

    MetaPixel::shouldReceive('isEnabled')->andReturn(true);
    MetaPixel::shouldReceive('pixelId')->andReturn('');
    MetaPixel::shouldReceive('testEnabled')->andReturn(false);

    $this->call(
        'POST',
        route('contact.store'),
        contactPayload(),
        ['cookie_consent' => json_encode(['necessary' => true, 'marketing' => true, 'analytics' => true])],
    )->assertRedirect();

    Bus::assertDispatched(
        SendMetaConversionEventJob::class,
        function (SendMetaConversionEventJob $job) {
            return $job->eventName === 'Lead'
                && $job->eventId === '11111111-2222-4333-8444-555555555555'
                && ($job->userDataAttributes['email'] ?? null) === 'john.doe@gmail.com'
                && ($job->userDataAttributes['phone'] ?? null) === '39123456789'
                && ($job->userDataAttributes['firstName'] ?? null) === 'john'
                && ($job->userDataAttributes['lastName'] ?? null) === 'doe';
        },
    );
});

test('does not call CAPI when marketing consent is missing', function () {
    Mail::fake();
    Bus::fake();

    MetaPixel::shouldReceive('isEnabled')->andReturn(true);
    MetaPixel::shouldReceive('pixelId')->andReturn('');
    MetaPixel::shouldReceive('testEnabled')->andReturn(false);

    $this->post(route('contact.store'), contactPayload())
        ->assertRedirect();

    Bus::assertNotDispatched(SendMetaConversionEventJob::class);
});
