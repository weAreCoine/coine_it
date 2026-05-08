<?php

declare(strict_types=1);

namespace App\Mail;

use App\Filament\Resources\Leads\Pages\EditLead;
use App\Models\Lead;
use App\Services\HealthCheckConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Lead $lead) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'site@coine.it',
            subject: 'Un nuovo lead da coine.it',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.lead-received',
            with: [
                'quizRecap' => $this->buildQuizRecap(),
                'quizRangeLabel' => $this->resolveRangeLabel(),
                'adminUrl' => $this->resolveAdminUrl(),
            ],
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Build a per-question recap of the health-check answers for display.
     *
     * @return array<int, array{question: string, answer: string, points: int|null, maxPoints: int}>
     */
    private function buildQuizRecap(): array
    {
        $answers = $this->lead->quiz_answers;

        if (empty($answers) || ! is_array($answers)) {
            return [];
        }

        $recap = [];

        foreach (HealthCheckConfig::questions() as $question) {
            $given = $answers[$question['key']] ?? null;
            $label = $this->matchOptionLabel($question['options'], $given['value'] ?? null);

            $recap[] = [
                'question' => (string) $question['text'],
                'answer' => $label,
                'points' => $question['scored'] ? (int) ($given['points'] ?? 0) : null,
                'maxPoints' => (int) $question['weight'],
            ];
        }

        return $recap;
    }

    /**
     * @param  array<int, array<string, mixed>>  $options
     */
    private function matchOptionLabel(array $options, ?string $value): string
    {
        if ($value === null) {
            return '— nessuna risposta —';
        }

        foreach ($options as $option) {
            if ($option['value'] === $value) {
                return (string) $option['label'];
            }
        }

        return $value;
    }

    private function resolveRangeLabel(): ?string
    {
        if ($this->lead->quiz_score === null) {
            return null;
        }

        $score = (int) $this->lead->quiz_score;

        foreach (HealthCheckConfig::quizConfig()['resultRanges'] as $range) {
            if ($score >= $range['min'] && $score <= $range['max']) {
                return (string) $range['label'];
            }
        }

        return null;
    }

    private function resolveAdminUrl(): ?string
    {
        try {
            return EditLead::getUrl(['record' => $this->lead]);
        } catch (\Throwable) {
            return null;
        }
    }
}
