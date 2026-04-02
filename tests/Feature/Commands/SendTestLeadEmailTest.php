<?php

use App\Mail\LeadReceived;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;

test('mail test lead command fails when no leads exist', function () {
    Mail::fake();

    $this->artisan('mail:test-lead')
        ->expectsOutputToContain('No leads found in the database.')
        ->assertFailed();
});

test('mail test lead command sends the latest lead to the default recipient', function () {
    Mail::fake();

    config(['mail.from.address' => 'ops@example.com']);

    $lead = Lead::factory()->create(['name' => 'Latest Lead']);

    $this->artisan('mail:test-lead')
        ->expectsOutputToContain('Test email sent to ops@example.com using lead: Latest Lead')
        ->assertSuccessful();

    Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) use ($lead) {
        return $mail->hasTo('ops@example.com')
            && $mail->lead->is($lead);
    });
});

test('mail test lead command uses the custom recipient override', function () {
    Mail::fake();

    $lead = Lead::factory()->create();

    $this->artisan('mail:test-lead --to=custom@example.com')
        ->expectsOutputToContain('Test email sent to custom@example.com')
        ->assertSuccessful();

    Mail::assertSent(LeadReceived::class, function (LeadReceived $mail) use ($lead) {
        return $mail->hasTo('custom@example.com')
            && $mail->lead->is($lead);
    });
});
