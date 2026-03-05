<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class DevelopingPageController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('services/developing', [
            'cardGrid' => [
                'kicker' => __('Il nostro metodo'),
                'title' => __('Creiamo un asset per il tuo business'),
                'subtitle' => __('Non realizziamo solo semplici siti o app. Costruiamo strumenti digitali progettati per funzionare nel tempo, integrarsi con il marketing e supportare decisioni concrete.'),
                'columns' => [
                    [
                        'icon' => $this->loadSvg('feature-3.svg'),
                        'title' => 'Analisi',
                        'description' => "Partiamo dagli obiettivi di business, non dalla tecnologia e creiamo una UX e flussi pensati per semplificare l'esperienza e favorire l'azione.",
                    ],
                    [
                        'icon' => $this->loadSvg('feature-1.svg'),
                        'title' => 'Sviluppo',
                        'description' => 'Creiamo codice pulito, manutenibile e scalabile. Utilizzando i coding agents per ridurre i tempi di sviluppo, non per demandare il lavoro.',
                    ],
                    [
                        'icon' => $this->loadSvg('feature-2.svg'),
                        'title' => 'Supporto',
                        'description' => 'Restiamo sempre al tuo fianco: supporto evolutivo, miglioramenti e aggiornamenti per far crescere il progetto insieme a te.',
                    ],
                ],
            ],
        ]);
    }

    private function loadSvg(string $filename): string
    {
        try {
            return File::get(public_path('svg/'.$filename));
        } catch (Throwable $exception) {
            ExceptionHandler::handle($exception);

            return '';
        }
    }
}
