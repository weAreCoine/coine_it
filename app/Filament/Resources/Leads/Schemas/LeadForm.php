<?php

declare(strict_types=1);

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStage;
use App\Enums\Services;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255),
                TextInput::make('website')
                    ->url()
                    ->maxLength(255),
                Textarea::make('project')
                    ->columnSpanFull(),
                TextInput::make('budget')
                    ->maxLength(255),
                Select::make('services')
                    ->multiple()
                    ->options(collect(Services::cases())
                        ->mapWithKeys(fn (Services $service) => [$service->value => $service->getLabel()])
                        ->all())
                    ->columnSpanFull(),
                Select::make('stage')
                    ->options(LeadStage::class)
                    ->default(LeadStage::NEW)
                    ->required(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('terms'),
                TextInput::make('quiz_score')
                    ->numeric()
                    ->disabled(),
                Textarea::make('quiz_answers')
                    ->disabled()
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : null),
            ]);
    }
}
