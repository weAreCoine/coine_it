<?php

declare(strict_types=1);

namespace App\Filament\Resources\CookieConsents\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CookieConsentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('choice_type')
                    ->label('Scelta')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'accept_all' => 'success',
                        'reject_all' => 'danger',
                        'custom' => 'warning',
                        'update' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                IconColumn::make('marketing')
                    ->label('Marketing')
                    ->boolean()
                    ->sortable(),
                IconColumn::make('analytics')
                    ->label('Analytics')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('path')
                    ->label('Path')
                    ->limit(50)
                    ->tooltip(fn ($record): ?string => $record->path)
                    ->toggleable(),
                TextColumn::make('version')
                    ->label('Versione')
                    ->toggleable(),
                TextColumn::make('external_id')
                    ->label('Visitor')
                    ->limit(8)
                    ->tooltip(fn ($record): ?string => $record->external_id)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('consent_id')
                    ->label('Consent ID')
                    ->limit(8)
                    ->tooltip(fn ($record): ?string => $record->consent_id)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('choice_type')
                    ->label('Tipo scelta')
                    ->options([
                        'accept_all' => 'Accetta tutti',
                        'reject_all' => 'Solo necessari',
                        'custom' => 'Personalizzato',
                        'update' => 'Aggiornamento',
                    ]),
                TernaryFilter::make('marketing')
                    ->label('Marketing'),
                TernaryFilter::make('analytics')
                    ->label('Analytics'),
                Filter::make('last_30_days')
                    ->label('Ultimi 30 giorni')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDays(30))),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
