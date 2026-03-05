<?php

namespace App\Filament\Resources\ProjectTags\Pages;

use App\Filament\Resources\ProjectTags\ProjectTagResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectTags extends ListRecords
{
    protected static string $resource = ProjectTagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
