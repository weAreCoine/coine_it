<?php

use App\Mail\LeadReceived;
use Illuminate\Support\Facades\Mail;

test('creates a lead with correct field mapping', function () {
    Mail::fake();

    $payload = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phone' => '+39123456789',
        'message' => 'I need a website',
        'termsAccepted' => true,
    ];

    $this->post(route('contact.store'), $payload)
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

    $payload = [
        'firstName' => 'Jane',
        'lastName' => 'Doe',
        'email' => 'jane@example.com',
        'phone' => null,
        'message' => 'Help me',
        'termsAccepted' => true,
    ];

    $this->post(route('contact.store'), $payload)
        ->assertOk();

    $this->assertDatabaseHas('leads', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone' => null,
    ]);
});

test('dispatches LeadCreated event and sends LeadReceived mailable', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'test@example.com',
        'phone' => null,
        'message' => 'Project description',
        'termsAccepted' => true,
    ]);

    Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) {
        return $mail->hasTo('luca@coine.it')
            && $mail->hasCc('silvia@coine.it');
    });
});

test('LeadReceived mailable has correct subject and from', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'firstName' => 'Test',
        'lastName' => 'User',
        'email' => 'mailable@example.com',
        'phone' => null,
        'message' => 'Check mailable',
        'termsAccepted' => true,
    ]);

    Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) {
        return $mail->hasFrom('site@coine.it')
            && $mail->hasSubject('Un nuovo lead da coine.it');
    });
});

test('validation requires firstName', function () {
    $this->post(route('contact.store'), [
        'lastName' => 'Doe',
        'email' => 'x@example.com',
        'message' => 'msg',
        'termsAccepted' => true,
    ])->assertSessionHasErrors('firstName');
});

test('validation requires lastName', function () {
    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'email' => 'x@example.com',
        'message' => 'msg',
        'termsAccepted' => true,
    ])->assertSessionHasErrors('lastName');
});

test('validation requires email', function () {
    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'message' => 'msg',
        'termsAccepted' => true,
    ])->assertSessionHasErrors('email');
});

test('validation requires valid email format', function () {
    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'not-an-email',
        'message' => 'msg',
        'termsAccepted' => true,
    ])->assertSessionHasErrors('email');
});

test('validation requires message', function () {
    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'x@example.com',
        'termsAccepted' => true,
    ])->assertSessionHasErrors('message');
});

test('stores newsletter_opt_in as true when newsletterOptIn is true', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'newsletter@example.com',
        'phone' => null,
        'message' => 'I want newsletter',
        'termsAccepted' => true,
        'newsletterOptIn' => true,
    ])->assertOk();

    $this->assertDatabaseHas('leads', [
        'email' => 'newsletter@example.com',
        'newsletter_opt_in' => true,
    ]);
});

test('stores newsletter_opt_in as false when newsletterOptIn is omitted', function () {
    Mail::fake();

    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'nonews@example.com',
        'phone' => null,
        'message' => 'No newsletter',
        'termsAccepted' => true,
    ])->assertOk();

    $this->assertDatabaseHas('leads', [
        'email' => 'nonews@example.com',
        'newsletter_opt_in' => false,
    ]);
});

test('validation requires termsAccepted', function () {
    $this->post(route('contact.store'), [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'x@example.com',
        'message' => 'msg',
        'termsAccepted' => false,
    ])->assertSessionHasErrors('termsAccepted');
});
