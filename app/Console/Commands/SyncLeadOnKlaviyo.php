<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Console\Command;

class SyncLeadOnKlaviyo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lead:to-klaviyo {lead}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync lead data to Klaviyo';

    /**
     * Execute the console command.
     */
    public function handle(KlaviyoService $klaviyoService): int
    {
        if (!KlaviyoService::isEnabled()) {
            $this->error('Klaviyo integration is disabled');
            return self::FAILURE;
        }

        $lead = $this->argument('lead');
        if (empty($lead)) {
            $this->error('Lead ID is required');
            return self::FAILURE;
        }

        $lead = Lead::find($lead);
        if (empty($lead)) {
            $this->error('Lead ID is not found');
            return self::FAILURE;
        }

        $klaviyoService->syncHealthCheckLead($lead);

        return self::SUCCESS;
    }
}
