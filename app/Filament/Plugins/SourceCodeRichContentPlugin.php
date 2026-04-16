<?php

declare(strict_types=1);

namespace App\Filament\Plugins;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\EditorCommand;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasToolbarButtons;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Forms\Components\Textarea;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class SourceCodeRichContentPlugin implements HasToolbarButtons, RichContentPlugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    /**
     * @return array<\Tiptap\Core\Extension>
     */
    public function getTipTapPhpExtensions(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    public function getTipTapJsExtensions(): array
    {
        return [];
    }

    /**
     * @return array<RichEditorTool>
     */
    public function getEditorTools(): array
    {
        return [
            RichEditorTool::make('sourceCode')
                ->action(arguments: '{ html: $getEditor().getHTML() }')
                ->icon(Heroicon::CodeBracket),
        ];
    }

    /**
     * @return array<Action>
     */
    public function getEditorActions(): array
    {
        return [
            Action::make('sourceCode')
                ->label('Sorgente HTML')
                ->modalWidth(Width::SevenExtraLarge)
                ->fillForm(fn (array $arguments): array => [
                    'html' => $arguments['html'] ?? '',
                ])
                ->schema([
                    Textarea::make('html')
                        ->label('HTML')
                        ->rows(20)
                        ->extraInputAttributes(['style' => 'font-family: monospace; font-size: 0.875rem;']),
                ])
                ->action(function (array $arguments, array $data, RichEditor $component): void {
                    $component->runCommands(
                        [
                            EditorCommand::make('setContent', arguments: [$data['html']]),
                        ],
                        editorSelection: $arguments['editorSelection'],
                    );
                }),
        ];
    }

    /**
     * @return array<string|array<string|array<string>>>
     */
    public function getEnabledToolbarButtons(): array
    {
        return ['sourceCode'];
    }

    /**
     * @return array<string>
     */
    public function getDisabledToolbarButtons(): array
    {
        return [];
    }
}
