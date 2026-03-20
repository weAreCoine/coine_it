<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\HealthCheckResultService;
use Inertia\Inertia;
use Inertia\Response;

class HealthCheckResultPageController extends Controller
{
    public function show(Lead $lead, HealthCheckResultService $resultService): Response
    {
        abort_unless($lead->quiz_score !== null, 404);

        $firstName = explode(' ', trim($lead->name ?? ''))[0];

        return Inertia::render('health-check-result', [
            'result' => $resultService->buildResultData($lead),
            'leadName' => $firstName,
        ]);
    }
}
