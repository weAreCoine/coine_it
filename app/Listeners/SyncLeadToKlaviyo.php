<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Services\Klaviyo\KlaviyoService;
use Illuminate\Contracts\Queue\ShouldQueue;

readonly class SyncLeadToKlaviyo implements ShouldQueue
{
    public function __construct(private KlaviyoService $klaviyoService)
    {
    }

    /**
     * Handle the LeadCreated event.
     */
    public function handle(LeadCreated $event): void
    {
        if (!KlaviyoService::isEnabled()) {
            return;
        }

        if ($event->lead->quiz_answers === null) {
            return;
        }

        $this->klaviyoService->syncHealthCheckLead($event->lead);
    }
}
