<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\CookieConsent;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CookieConsentStatsWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $since = now()->subDays(30);

        $base = CookieConsent::query()->where('created_at', '>=', $since);

        $total = (clone $base)->count();
        $marketingAccepted = $total === 0 ? 0 : (clone $base)->where('marketing', true)->count();
        $analyticsAccepted = $total === 0 ? 0 : (clone $base)->where('analytics', true)->count();
        $rejectAll = $total === 0 ? 0 : (clone $base)->where('choice_type', 'reject_all')->count();

        return [
            Stat::make('Consensi totali (30g)', (string) $total)
                ->icon('heroicon-o-shield-check')
                ->color('primary'),
            Stat::make('Marketing accettato', $this->formatRate($marketingAccepted, $total))
                ->description($marketingAccepted.' su '.$total)
                ->icon('heroicon-o-megaphone')
                ->color($this->rateColor($marketingAccepted, $total)),
            Stat::make('Analytics accettato', $this->formatRate($analyticsAccepted, $total))
                ->description($analyticsAccepted.' su '.$total)
                ->icon('heroicon-o-chart-bar')
                ->color($this->rateColor($analyticsAccepted, $total)),
            Stat::make('Reject-all (30g)', $this->formatRate($rejectAll, $total))
                ->description($rejectAll.' su '.$total)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }

    private function formatRate(int $part, int $total): string
    {
        if ($total === 0) {
            return '—';
        }

        return number_format(($part / $total) * 100, 1).'%';
    }

    private function rateColor(int $part, int $total): string
    {
        if ($total === 0) {
            return 'gray';
        }

        $ratio = $part / $total;

        return match (true) {
            $ratio >= 0.66 => 'success',
            $ratio >= 0.33 => 'warning',
            default => 'danger',
        };
    }
}
