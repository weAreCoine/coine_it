<?php

declare(strict_types=1);

namespace App\Services\GoogleAnalytics;

use Br33f\Ga4\MeasurementProtocol\Dto\Event\BaseEvent;
use Br33f\Ga4\MeasurementProtocol\Dto\Request\BaseRequest;
use Br33f\Ga4\MeasurementProtocol\Dto\Response\BaseResponse;
use Br33f\Ga4\MeasurementProtocol\Service as Ga4Service;

/**
 * Low-level client wrapping br33f/php-ga4-mp for GA4 Measurement Protocol.
 */
class GoogleAnalyticsClient
{
    private Ga4Service $ga4;

    public function __construct()
    {
        $this->ga4 = new Ga4Service(config('google-analytics.api_secret'));
        $this->ga4->setMeasurementId(config('google-analytics.measurement_id'));
    }

    /**
     * Send an event to GA4 via Measurement Protocol.
     *
     * @param  string  $clientId  GA client_id (from _ga cookie)
     * @param  string  $eventName  e.g. 'page_view', 'generate_lead'
     * @param  array<string, mixed>  $params  Event parameters
     */
    public function sendEvent(string $clientId, string $eventName, array $params = []): BaseResponse
    {
        $event = new BaseEvent($eventName);

        foreach ($params as $key => $value) {
            $event->setParamValue($key, $value);
        }

        $request = new BaseRequest;
        $request->setClientId($clientId);
        $request->addEvent($event);

        return $this->ga4->send($request);
    }
}
