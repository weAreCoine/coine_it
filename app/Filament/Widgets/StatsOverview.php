<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\LeadStage;
use App\Models\Article;
use App\Models\Lead;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $stats = [
            Stat::make('Data odierna', Carbon::today()->translatedFormat('d F Y'))
                ->icon('heroicon-o-calendar'),
            Stat::make('Articoli pubblicati', Article::query()->where('is_published', true)->count())
                ->icon('heroicon-o-document-text')
                ->color('success'),
            Stat::make('Progetti pubblicati', Project::query()->where('is_published', true)->count())
                ->icon('heroicon-o-briefcase')
                ->color('success'),
        ];

        foreach (LeadStage::cases() as $stage) {
            $count = Lead::query()->where('stage', $stage)->count();

            if ($count > 0) {
                $stats[] = Stat::make('Lead: '.$stage->getLabel(), $count)
                    ->color($stage->getColor());
            }
        }

        return $stats;
    }
}
