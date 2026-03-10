<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Mail\LeadReceived;
use App\Models\Lead;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestLeadEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test-lead {--to= : Override recipient email address}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test LeadReceived email using the latest lead';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $lead = Lead::latest()->first();

        if (! $lead) {
            $this->error('No leads found in the database.');

            return self::FAILURE;
        }

        $recipient = $this->option('to') ?? config('mail.from.address');

        Mail::to($recipient)->send(new LeadReceived($lead));

        $this->info("Test email sent to {$recipient} using lead: {$lead->name}");

        return self::SUCCESS;
    }
}
