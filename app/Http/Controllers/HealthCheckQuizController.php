<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\HealthCheckQuizRequest;
use App\Models\Lead;
use App\Services\Klaviyo\KlaviyoService;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HealthCheckQuizController extends Controller
{
    /**
     * Mirror the browser-side `startQuiz` custom Meta Pixel event onto the
     * Conversions API using the same event id, so that Meta can de-duplicate
     * the two firings.
     */
    public function start(Request $request, LeadService $leadService): JsonResponse
    {
        $validated = $request->validate([
            'eventId' => 'required|uuid',
        ]);

        $leadService->trackMetaPixelEventServerSide('startQuiz', $validated['eventId']);

        return response()->json(null, 204);
    }

    /**
     * Store the quiz lead (Event 2: Lead).
     */
    public function store(HealthCheckQuizRequest $request, LeadService $leadService): void
    {
        $validated = $request->validated();

        $leadService->createAndTrack([
            'email' => $validated['email'],
            'name' => sprintf('%s %s', $validated['firstName'], $validated['lastName']),
            'phone' => $validated['phone'],
            'website' => $validated['url'],
            'terms' => true,
            'quiz_answers' => $validated['answers'],
            'quiz_score' => $validated['score'],
            'newsletter_opt_in' => true,
        ], $request);
    }

    /**
     * Complete the quiz (Event 3: QuizCompleted).
     */
    public function complete(Request $request, LeadService $leadService, KlaviyoService $klaviyoService): void
    {
        $validated = $request->validate([
            'email' => 'required|email|indisposable|max:255',
            'openText' => 'nullable|string|max:2000',
        ], [
            'email.indisposable' => 'Non sono ammessi indirizzi email temporanei.',
        ]);

        $lead = Lead::where('email', $validated['email'])->first();

        if ($lead && ! empty($validated['openText'])) {
            $lead->update(['notes' => $validated['openText']]);

            if (KlaviyoService::isEnabled()) {
                $profileId = $klaviyoService->findProfileIdByEmail($lead->email);

                if ($profileId !== null) {
                    $klaviyoService->updateProfileProperties($profileId, [
                        'health_check_notes' => $validated['openText'],
                    ]);
                }
            }
        }

        $leadService->trackMetaPixelEvent(
            'CompleteRegistration',
            $validated['email'],
            $lead?->phone,
        );
        $leadService->trackGAEvent($request, 'quiz_completed');
        $leadService->trackLinkedInEvent($request, 'complete_registration', $validated['email']);
    }
}
