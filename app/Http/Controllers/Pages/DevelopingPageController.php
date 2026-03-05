<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Abstracts\AbstractPageController;
use App\Services\ClientsLogosService;
use Inertia\Inertia;
use Inertia\Response;

class DevelopingPageController extends AbstractPageController
{
    public function show(): Response
    {
        return Inertia::render('services/developing', [
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
                        'description' => 'Restiamo sempre al tuo fianco: supporto evolutivo, con contatto diretto, miglioramenti e aggiornamenti per far crescere il progetto insieme a te.',
                    ],
                ],
            ],
        ]);
    }
}
