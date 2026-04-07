<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->columnSpanFull()
                    ->required(),
                RichEditor::make('content')
                    ->extraInputAttributes(['style' => 'min-height: 20rem; max-height: 50vh; overflow-y: auto;'])
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Select::make('client_logo')
                    ->label('Logo Cliente')
                    ->options(function () {
                        return collect(File::files(public_path('images/clients')))
                            ->filter(fn ($file) => in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                            ->mapWithKeys(fn ($file) => [
                                $file->getFilename() => Str::headline($file->getFilenameWithoutExtension()),
                            ])
                            ->all();
                    })
                    ->searchable()
                    ->nullable()
                    ->helperText('Associa questo progetto a un logo cliente nel marquee'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required(),
                Select::make('categories')
                    ->multiple()
                    ->relationship('categories', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome Categoria')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descrizione')
                            ->required()
                            ->maxLength(255),
                    ]),
                Select::make('tags')
                    ->multiple()
                    ->relationship('tags', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome Tag')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descrizione')
                            ->required()
                            ->maxLength(255),
                    ]),
                Select::make('tools')
                    ->label('Strumenti')
                    ->multiple()
                    ->relationship('tools', 'name')
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('Nome Tool')
                            ->required()
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Descrizione')
                            ->required()
                            ->maxLength(255),
                    ]),
                TextInput::make('goal')
                    ->label('Obiettivo')
                    ->maxLength(255),
                TextInput::make('results')
                    ->label('Risultati')
                    ->maxLength(255),
                TextInput::make('seo_title'),
                TextInput::make('seo_description'),
                FileUpload::make('cover')
                    ->disk(Project::$disk)
                    ->storeFileNamesIn('original_filename')
                    ->directory('images/'.now()->format('Y/m'))
                    ->visibility('public')
                    ->maxSize(1024 * 2)
                    ->openable()
                    ->image(),
                FileUpload::make('seo_image')
                    ->disk(Project::$disk)
                    ->storeFileNamesIn('original_filename')
                    ->directory('images/'.now()->format('Y/m'))
                    ->visibility('public')
                    ->maxSize(1024 * 2)
                    ->openable()
                    ->image(),
                Toggle::make('is_published')
                    ->required(),
                Toggle::make('is_featured')
                    ->required(),
            ]);
    }
}
