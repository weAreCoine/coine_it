<?php

use App\Mail\LeadReceived;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

function validQuizPayload(array $overrides = []): array
{
    return array_merge([
        'firstName' => 'Mario',
        'lastName' => 'Rossi',
        'email' => 'mario@example.com',
        'phone' => '+39123456789',
        'url' => 'https://www.example.com',
        'marketingConsent' => true,
        'answers' => [
            'platform' => ['value' => 'shopify', 'points' => 0],
            'advertising' => ['value' => 'freelance', 'points' => 9],
            'coordination' => ['value' => 'external', 'points' => 7],
            'tracking' => ['value' => 'basic', 'points' => 8],
            'mobile' => ['value' => 'ok', 'points' => 13],
            'retention' => ['value' => 'basic', 'points' => 6],
        ],
        'score' => 43,
    ], $overrides);
}

test('creates a lead with correct field mapping', function () {
    Mail::fake();

    $this->post(route('health-check.store'), validQuizPayload())
        ->assertOk();

    $this->assertDatabaseHas('leads', [
        'name' => 'Mario Rossi',
        'email' => 'mario@example.com',
        'phone' => '+39123456789',
        'website' => 'https://www.example.com',
        'terms' => true,
        'quiz_score' => 43,
    ]);
});

test('stores quiz_answers as JSON', function () {
    Mail::fake();

    $this->post(route('health-check.store'), validQuizPayload())
        ->assertOk();

    $lead = Lead::where('email', 'mario@example.com')->first();
    expect($lead->quiz_answers)->toBeArray()
        ->and($lead->quiz_answers['advertising']['points'])->toBe(9);
});

test('duplicate email creates separate leads', function () {
    Mail::fake();

    $this->post(route('health-check.store'), validQuizPayload())
        ->assertOk();

    $this->post(route('health-check.store'), validQuizPayload([
        'firstName' => 'Mario Updated',
        'score' => 70,
    ]))->assertOk();

    expect(Lead::where('email', 'mario@example.com')->count())->toBe(2);
});

test('dispatches LeadCreated event and sends LeadReceived mailable', function () {
    Mail::fake();

    $this->post(route('health-check.store'), validQuizPayload());

    Mail::assertSent(LeadReceived::class);
});

test('validation requires firstName', function () {
    $this->post(route('health-check.store'), validQuizPayload(['firstName' => '']))
        ->assertSessionHasErrors('firstName');
});

test('validation requires email', function () {
    $this->post(route('health-check.store'), validQuizPayload(['email' => '']))
        ->assertSessionHasErrors('email');
});

test('validation requires valid email', function () {
    $this->post(route('health-check.store'), validQuizPayload(['email' => 'not-an-email']))
        ->assertSessionHasErrors('email');
});

test('validation requires url', function () {
    $this->post(route('health-check.store'), validQuizPayload(['url' => '']))
        ->assertSessionHasErrors('url');
});

test('validation requires marketingConsent to be accepted', function () {
    $this->post(route('health-check.store'), validQuizPayload(['marketingConsent' => false]))
        ->assertSessionHasErrors('marketingConsent');
});

test('validation requires answers', function () {
    $this->post(route('health-check.store'), validQuizPayload(['answers' => []]))
        ->assertSessionHasErrors('answers');
});

test('validation requires score', function () {
    $this->post(route('health-check.store'), validQuizPayload(['score' => null]))
        ->assertSessionHasErrors('score');
});

test('complete updates notes with openText', function () {
    Mail::fake();

    $this->post(route('health-check.store'), validQuizPayload())
        ->assertOk();

    $this->patch(route('health-check.complete'), [
        'email' => 'mario@example.com',
        'openText' => 'Il nostro problema principale è il checkout mobile.',
    ])->assertOk();

    $lead = Lead::where('email', 'mario@example.com')->first();
    expect($lead->notes)->toBe('Il nostro problema principale è il checkout mobile.');
});

test('complete without openText does not clear notes', function () {
    Mail::fake();

    $this->post(route('health-check.store'), validQuizPayload())
        ->assertOk();

    $lead = Lead::where('email', 'mario@example.com')->first();
    $lead->update(['notes' => 'existing notes']);

    $this->patch(route('health-check.complete'), [
        'email' => 'mario@example.com',
        'openText' => '',
    ])->assertOk();

    $lead->refresh();
    expect($lead->notes)->toBe('existing notes');
});
