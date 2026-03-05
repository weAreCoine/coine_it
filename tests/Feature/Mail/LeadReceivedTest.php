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
