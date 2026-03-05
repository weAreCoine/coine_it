<?php
declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Services\ClientsLogosService;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MarketingPageController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('services/marketing', [
            'faqs' => [
                [
                    'question' => __('Quanto tempo ci vuole per sviluppare un sito web o un\'app?'),
                    'answer' => __('La durata media dei progetti può variare in base alla complessità e alle funzionalità richieste. Ci piace in fase di consulenza analizzare ogni progetto per garantire soluzioni su misura.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quali tecnologie utilizzate per lo sviluppo?'),
                    'answer' => __('Utilizziamo diverse tecnologie in base alle tue esigenze. Diamo molta importanza alle fasi di analisi e progettazione, che ci permettono di comprendere a fondo le specifiche del progetto e individuare gli strumenti più indicati.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quali sono i costi per lo sviluppo di un sito o un\'app?'),
                    'answer' => __('I costi variano a seconda delle caratteristiche, delle personalizzazioni e delle integrazioni richieste. Partiamo dalle tue esigenze e dal budget che hai a disposizione per realizzare un preventivo ad hoc.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Offrite assistenza e manutenzione post-lancio?'),
                    'answer' => __('Assicuriamo servizi di supporto, aggiornamenti e manutenzione, garantendo la continuità del progetto anche dopo la consegna.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Posso integrare funzionalità specifiche come pagamenti online o CRM?'),
                    'answer' => __('È possibile aggiungere funzionalità come e-commerce, pagamenti, gestione dei contatti (CRM), e altre integrazioni personalizzate.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Cosa succede se voglio apportare modifiche durante lo sviluppo?'),
                    'answer' => __('Gestiamo le richieste di modifica con una discreta tolleranza. Valutiamo in base alla richiesta eventuali costi aggiuntivi che ti comunichiamo prima di procedere con il lavoro.'),
                    'opened' => false,
                ],
            ],
            'marquee' => [
                'kicker' => __('Portfolio'),
                'title' => __('Dieci anni di successi: ecco alcuni amici cresciuti con noi.'),
                'subtitle' => __('Collaboriamo con realtà diverse accompagnandole nella crescita digitale, unendo tecnologia e marketing per costruire soluzioni coerenti con le reali esigenze del business.'),
                'link' => null,
                'slides' => ClientsLogosService::all()
            ],
            'cardGrid' => [
                'kicker' => __('Il nostro metodo'),
                'title' => __('Creiamo un asset per il tuo business'),
                'subtitle' => __('Non realizziamo solo semplici siti o app. Costruiamo strumenti digitali progettati per funzionare nel tempo, integrarsi con il marketing e supportare decisioni concrete.'),
                'columns' => [
                    [
                        'icon' => $this->loadSvg('feature-3.svg'),
                        'title' => 'Comprensione del contesto',
                        'description' => "Iniziamo sempre dall'analisi del contesto e dalla comprensione degli obiettivi aziendali così da rendere misurabile il rendimento dell'investimento pubblicitario.",
                    ],
                    [
                        'icon' => $this->loadSvg('feature-1.svg'),
                        'title' => 'Strategia',
                        'description' => 'Colleghiamo le campagne ai dati reali di vendita: L’obiettivo è rendere il percorso più fluido e ridurre i punti di frizione che bloccano la vendita.',
                    ],
                    [
                        'icon' => $this->loadSvg('feature-2.svg'),
                        'title' => 'Operatività e reporting',
                        'description' => 'Ottimizzazione continua delle campagne, guidati dai dati raccolti invece che da tentativi, fornendo report dettagliati e analisi per migliorare le performance.',
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
