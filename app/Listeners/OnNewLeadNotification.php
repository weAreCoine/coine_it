<?php

namespace App\Listeners;

use App\Events\LeadCreated;
use App\Mail\LeadReceived;
use Illuminate\Support\Facades\Mail;

class OnNewLeadNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LeadCreated $event): void
    {
        Mail::to('luca@coine.it')
            ->cc('silvia@coine.it')
            ->send(new LeadReceived($event->lead));
    }
}
