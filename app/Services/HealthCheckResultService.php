<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lead;

class HealthCheckResultService
{
    /**
     * Build all result data needed for the frontend.
     *
     * @return array{
     *     score: int,
     *     maxScore: int,
     *     rangeColor: string,
     *     rangeLabel: string,
     *     rangeMessage: string,
     *     motivationalTitle: string,
     *     motivationalText: string,
     *     benchmarkText: string,
     *     findings: array<int, array{color: string, title: string, description: string}>
     * }
     */
    public function buildResultData(Lead $lead): array
    {
        $questions = HealthCheckConfig::questions();
        $config = HealthCheckConfig::quizConfig();
        $answers = $lead->quiz_answers ?? [];
        $score = (int) $lead->quiz_score;

        $range = $this->findRange($score, $config['resultRanges']);
        $findings = $this->buildFindings($questions, $answers, $config);

        return [
            'score' => $score,
            'maxScore' => 100,
            'rangeColor' => $range['color'],
            'rangeLabel' => $range['label'],
            'rangeMessage' => $range['message'],
            'motivationalTitle' => $range['motivational_title'],
            'motivationalText' => $range['motivational_text'],
            'benchmarkText' => $config['benchmarkText'],
            'findings' => $findings,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $ranges
     * @return array<string, mixed>
     */
    private function findRange(int $score, array $ranges): array
    {
        foreach ($ranges as $range) {
            if ($score >= $range['min'] && $score <= $range['max']) {
                return $range;
            }
        }

        return end($ranges);
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     * @param  array<string, array{value: string, points: int}>  $answers
     * @param  array<string, mixed>  $config
     * @return array<int, array{color: string, title: string, description: string}>
     */
    private function buildFindings(array $questions, array $answers, array $config): array
    {
        $scoredQuestions = array_filter($questions, fn (array $q) => $q['scored'] && $q['finding'] !== null);

        $negatives = [];
        $positives = [];

        foreach ($scoredQuestions as $q) {
            $score = $answers[$q['key']]['points'] ?? 0;
            $finding = $q['finding'];

            if ($score <= $finding['threshold_max']) {
                $negatives[] = ['question' => $q, 'weight' => $q['weight']];
            }
            if ($score >= $finding['threshold_min']) {
                $positives[] = ['question' => $q, 'weight' => $q['weight']];
            }
        }

        usort($negatives, fn ($a, $b) => $b['weight'] <=> $a['weight']);
        usort($positives, fn ($a, $b) => $b['weight'] <=> $a['weight']);

        $findings = [];

        if (count($negatives) === 0) {
            foreach (array_slice($positives, 0, 2) as $p) {
                $findings[] = [
                    'color' => 'g',
                    'title' => $p['question']['finding']['positive_title'],
                    'description' => $p['question']['finding']['positive_text'],
                ];
            }
            $findings[] = [
                'color' => 'a',
                'title' => $config['fallbackFinding']['title'],
                'description' => $config['fallbackFinding']['text'],
            ];
        } else {
            foreach (array_slice($negatives, 0, 2) as $n) {
                $findings[] = [
                    'color' => 'r',
                    'title' => $n['question']['finding']['negative_title'],
                    'description' => $n['question']['finding']['negative_text'],
                ];
            }

            if (count($positives) > 0) {
                $p = $positives[0];
                $findings[] = [
                    'color' => 'g',
                    'title' => $p['question']['finding']['positive_title'],
                    'description' => $p['question']['finding']['positive_text'],
                ];
            } else {
                $areaLabels = HealthCheckConfig::areaLabels();
                $bestRatio = -1;
                $bestQ = null;

                foreach ($scoredQuestions as $q) {
                    $ratio = ($answers[$q['key']]['points'] ?? 0) / $q['weight'];
                    if ($ratio > $bestRatio) {
                        $bestRatio = $ratio;
                        $bestQ = $q;
                    }
                }

                if ($bestQ !== null) {
                    $areaLabel = $areaLabels[$bestQ['key']] ?? $bestQ['key'];
                    $findings[] = [
                        'color' => 'g',
                        'title' => 'Il tuo punto di forza',
                        'description' => "L'area dove sei più avanti è {$areaLabel} — un buon punto di partenza su cui costruire.",
                    ];
                }
            }
        }

        return $findings;
    }
}
