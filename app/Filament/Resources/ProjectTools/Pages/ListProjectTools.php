<?php

namespace App\Filament\Resources\ProjectTools\Pages;

use App\Filament\Resources\ProjectTools\ProjectToolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectTools extends ListRecords
{
    protected static string $resource = ProjectToolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
