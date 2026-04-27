<?php

use App\Mail\LeadReceived;
use Illuminate\Support\Facades\Mail;

function contactPayload(array $overrides = []): array
{
    return array_merge([
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+39123456789',
        'message' => 'I need a website',
        'termsAccepted' => true,
        'metaEventId' => '11111111-2222-4333-8444-555555555555',
    ], $overrides);
}

test('creates a lead with correct field mapping', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload())
        ->assertOk();

    $this->assertDatabaseHas('leads', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '+39123456789',
        'project' => 'I need a website',
        'terms' => true,
    ]);
});

test('accepts nullable phone', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'firstName' => 'Jane',
        'email' => 'jane@example.com',
        'phone' => null,
        'message' => 'Help me',
    ]))->assertOk();

    $this->assertDatabaseHas('leads', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => null,
    ]);
});

test('dispatches LeadCreated event and sends LeadReceived mailable', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'test@example.com',
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
        'email' => 'mailable@example.com',
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
        'email' => 'newsletter@example.com',
        'phone' => null,
        'message' => 'I want newsletter',
        'newsletterOptIn' => true,
    ]))->assertOk();

    $this->assertDatabaseHas('leads', [
        'email' => 'newsletter@example.com',
        'newsletter_opt_in' => true,
    ]);
});

test('stores newsletter_opt_in as false when newsletterOptIn is omitted', function () {
    Mail::fake();

    $this->post(route('contact.store'), contactPayload([
        'email' => 'nonews@example.com',
        'phone' => null,
        'message' => 'No newsletter',
    ]))->assertOk();

    $this->assertDatabaseHas('leads', [
        'email' => 'nonews@example.com',
        'newsletter_opt_in' => false,
    ]);
});

test('validation requires termsAccepted', function () {
    $this->post(route('contact.store'), contactPayload(['termsAccepted' => false]))
        ->assertSessionHasErrors('termsAccepted');
});
