<?php

use App\Mail\LeadReceived;
use Database\Factories\LeadFactory;

test('mailable has correct subject', function () {
    $lead = (new LeadFactory)->create();
    $mailable = new LeadReceived($lead);

    $mailable->assertHasSubject('Un nuovo lead da coine.it');
});

test('mailable has correct from address', function () {
    $lead = (new LeadFactory)->create();
    $mailable = new LeadReceived($lead);

    $mailable->assertFrom('site@coine.it');
});

test('mailable uses the correct view', function () {
    $lead = (new LeadFactory)->create();
    $mailable = new LeadReceived($lead);

    expect($mailable->content()->view)->toBe('mail.lead-received');
});

test('mailable has no attachments', function () {
    $lead = (new LeadFactory)->create();
    $mailable = new LeadReceived($lead);

    expect($mailable->attachments())->toBeEmpty();
});

test('mailable exposes the lead', function () {
    $lead = (new LeadFactory)->create();
    $mailable = new LeadReceived($lead);

    expect($mailable->lead->id)->toBe($lead->id);
});

test('rendered email shows contact fields and omits removed legacy fields', function () {
    $lead = (new LeadFactory)->create([
        'name' => 'Mario Rossi',
        'email' => 'mario.rossi@example.com',
        'phone' => '+39 333 1234567',
        'project' => 'Vorrei rifare il sito',
        'newsletter_opt_in' => true,
    ]);

    $html = (new LeadReceived($lead))->render();

    expect($html)
        ->toContain('Mario Rossi')
        ->toContain('mario.rossi@example.com')
        ->toContain('+39 333 1234567')
        ->toContain('Vorrei rifare il sito')
        ->toContain('Newsletter')
        ->toContain('Sì, vuole iscriversi')
        ->not->toContain('Services:')
        ->not->toContain('Budget:');
});

test('rendered email omits health check section when no quiz answers', function () {
    $lead = (new LeadFactory)->create([
        'quiz_answers' => null,
        'quiz_score' => null,
    ]);

    $html = (new LeadReceived($lead))->render();

    expect($html)->not->toContain('Health Check');
});

test('rendered email includes quiz score and per-question recap when quiz answers present', function () {
    $lead = (new LeadFactory)->withHealthCheck()->create([
        'quiz_answers' => [
            'platform' => ['value' => 'shopify', 'points' => 0],
            'advertising' => ['value' => 'agency', 'points' => 12],
            'coordination' => ['value' => 'internal', 'points' => 25],
            'tracking' => ['value' => 'complete', 'points' => 25],
            'mobile' => ['value' => 'optimized', 'points' => 20],
            'retention' => ['value' => 'advanced', 'points' => 18],
        ],
        'quiz_score' => 100,
    ]);

    $html = (new LeadReceived($lead))->render();

    expect($html)
        ->toContain('Health Check')
        ->toContain('100')
        ->toContain('/100')
        ->toContain('Il tuo e-commerce ha basi solide')
        ->toContain('Shopify')
        ->toContain('agenzia strutturata')
        ->toContain('12/12 pt');
});

test('rendered email includes open notes when present', function () {
    $lead = (new LeadFactory)->withHealthCheck()->create([
        'notes' => 'Vorrei concentrarmi sul checkout mobile',
    ]);

    $html = (new LeadReceived($lead))->render();

    expect($html)->toContain('Vorrei concentrarmi sul checkout mobile');
});
