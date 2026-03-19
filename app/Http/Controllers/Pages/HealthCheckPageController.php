<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class HealthCheckPageController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('health-check', [
            'heroPoints' => [
                [
                    'title' => __('Quando una landing page non converte'),
                    'description' => __('non aspetti un altro fornitore. La modifica avviene nello stesso flusso di chi ha progettato la campagna.'),
                ],
                [
                    'title' => __('Chi scrive i contenuti'),
                    'description' => __('sa già quali keyword performano nelle campagne a pagamento. Piano editoriale e piano media parlano la stessa lingua.'),
                ],
                [
                    'title' => __('Retainer fisso'),
                    'description' => __('non percentuale sulla spesa ads. I KPI sono concordati prima di partire. Ogni mese: cosa ha funzionato, cosa cambia.'),
                ],
                [
                    'title' => __('Massimo 4–6 clienti'),
                    'description' => __('in parallelo, per garantire attenzione piena a ogni progetto.'),
                ],
            ],
            'steps' => [
                [
                    'number' => '01',
                    'title' => __('Prima misuriamo'),
                    'description' => __('Il primo periodo è dedicato all\'analisi: tracking, funnel, dati storici. Nessuna campagna parte prima di avere una lettura chiara di dove si perdono le conversioni e perché.'),
                ],
                [
                    'number' => '02',
                    'title' => __('Sito e campagne in parallelo'),
                    'description' => __('Lo stesso professionista che imposta la campagna può modificare il template, correggere il data layer e ottimizzare il checkout — nello stesso flusso di lavoro, senza ticket aperti a terzi.'),
                ],
                [
                    'number' => '03',
                    'title' => __('Contenuti che supportano le performance'),
                    'description' => __('I contenuti nascono sapendo già quali keyword performano in campagna e quali touchpoint del funnel hanno più frizione. Niente di decorativo.'),
                ],
                [
                    'number' => '04',
                    'title' => __('Ottimizzazione continua'),
                    'description' => __('Il lavoro non finisce al lancio. Ogni mese: analisi dei dati, aggiustamenti, report trasparenti. L\'obiettivo è costruire un sistema che migliora nel tempo.'),
                ],
            ],
            'faqs' => [
                [
                    'question' => __('Come siete diversi da un\'agenzia tradizionale?'),
                    'answer' => __('Lavori direttamente con noi, non con un account che fa da filtro.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Come siete diversi da tre freelance separati?'),
                    'answer' => __('Advertising, sviluppo e contenuti condividono gli stessi dati e obiettivi.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quanto tempo prima di vedere risultati?'),
                    'answer' => __('Il primo mese è analisi. Dal secondo si interviene. Dal terzo si misurano i risultati.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Qual è il vostro modello di pricing?'),
                    'answer' => __('Retainer mensile fisso, con KPI concordati prima di iniziare.'),
                    'opened' => false,
                ],
            ],
            'questions' => [
                [
                    'id' => 1,
                    'text' => __('Qual è la tua piattaforma e-commerce?'),
                    'hint' => __('La piattaforma determina le possibilità di integrazione e le opzioni di ottimizzazione tecnica.'),
                    'key' => 'platform',
                    'options' => [
                        ['label' => 'WooCommerce', 'value' => 'woocommerce', 'points' => 0],
                        ['label' => 'Shopify', 'value' => 'shopify', 'points' => 0],
                        ['label' => 'PrestaShop / Magento', 'value' => 'prestashop', 'points' => 0],
                        ['label' => __('Sviluppo custom o altra piattaforma'), 'value' => 'custom', 'points' => 0],
                    ],
                ],
                [
                    'id' => 2,
                    'text' => __('Come gestisci l\'advertising in questo momento?'),
                    'hint' => __('La gestione delle campagne è spesso il primo punto dove si perde efficienza.'),
                    'key' => 'ads',
                    'options' => [
                        ['label' => __('Non faccio ancora advertising'), 'value' => 'none', 'points' => 0],
                        ['label' => __('Gestisco le campagne internamente'), 'value' => 'self', 'points' => 0],
                        ['label' => __('Ho un freelance o una piccola agenzia'), 'value' => 'freelance', 'points' => 0],
                        ['label' => __('Lavoro con un\'agenzia strutturata'), 'value' => 'agency', 'points' => 0],
                    ],
                ],
                [
                    'id' => 3,
                    'text' => __('Qual è il problema principale che vuoi affrontare?'),
                    'hint' => __('Scegli quello che senti più urgente.'),
                    'key' => 'pain',
                    'options' => [
                        ['label' => __('Ho traffico ma il sito non converte'), 'value' => 'traffic', 'points' => 0],
                        ['label' => __('Investo in advertising ma il ritorno è basso o instabile'), 'value' => 'roas', 'points' => 0],
                        ['label' => __('Marketing e sviluppo non sono coordinati, perdo tempo'), 'value' => 'silos', 'points' => 0],
                        ['label' => __('Non so dove intervenire — mancano i dati giusti'), 'value' => 'data', 'points' => 0],
                    ],
                ],
                [
                    'id' => 4,
                    'text' => __('Come valuti il tracking e gli analytics del tuo sito?'),
                    'hint' => __('Il tracking è la base di ogni decisione. Dati parziali o errati portano a ottimizzazioni nella direzione sbagliata.'),
                    'key' => 'tracking',
                    'options' => [
                        ['label' => __('Non ho configurato quasi nulla'), 'value' => 'none', 'points' => 0],
                        ['label' => __('Ho GA4, ma non mi fido pienamente dei dati'), 'value' => 'basic', 'points' => 0],
                        ['label' => __('Ho un setup discreto, ma so che ci sono lacune'), 'value' => 'partial', 'points' => 0],
                        ['label' => __('Tracking completo: GA4, GTM, eventi e-commerce, pixel'), 'value' => 'full', 'points' => 0],
                    ],
                ],
                [
                    'id' => 5,
                    'text' => __('Come descrivi l\'esperienza di acquisto su mobile?'),
                    'hint' => __('Oltre il 60% degli acquisti avviene da smartphone. Il checkout mobile è spesso il punto di frizione più sottovalutato.'),
                    'key' => 'checkout',
                    'options' => [
                        ['label' => __('Lento o poco ottimizzato — è un problema che riconosco'), 'value' => 'bad', 'points' => 0],
                        ['label' => __('Funziona, ma non l\'abbiamo mai testato in modo sistematico'), 'value' => 'ok', 'points' => 0],
                        ['label' => __('Buono: pochi step, caricamento veloce, ottimizzato'), 'value' => 'good', 'points' => 0],
                        ['label' => __('Non monitoro questo aspetto'), 'value' => 'unknown', 'points' => 0],
                    ],
                ],
                [
                    'id' => 6,
                    'text' => __('Da quanto tempo gestisci questo e-commerce?'),
                    'hint' => __('La maturità del progetto cambia le priorità di intervento e le aspettative sui tempi.'),
                    'key' => 'age',
                    'options' => [
                        ['label' => __('Meno di un anno — stiamo ancora costruendo la base'), 'value' => 'new', 'points' => 0],
                        ['label' => __('1–3 anni — abbiamo una storia, vogliamo crescere'), 'value' => 'mid', 'points' => 0],
                        ['label' => __('Più di 3 anni — vogliamo ottimizzare e scalare'), 'value' => 'mature', 'points' => 0],
                        ['label' => __('Più di 3 anni — ma sentiamo che qualcosa non funziona'), 'value' => 'restructure', 'points' => 0],
                    ],
                ],
            ],
            'teamMembers' => [
                [
                    'name' => 'Luca',
                    'role' => __('Performance Marketing · Sviluppo'),
                    'bio' => __('Si occupa di campagne Google Ads, Meta Ads e LinkedIn Ads, e sviluppa sull\'infrastruttura tecnica dell\'e-commerce — in PHP/Laravel, WordPress/WooCommerce e Flutter. La possibilità di intervenire su entrambi i livelli nello stesso progetto elimina i tempi morti tra chi pianifica le campagne e chi costruisce o modifica il sito.'),
                    'image' => '/images/luca.webp',
                    'tags' => ['Google Ads', 'Meta Ads', 'Laravel', 'WooCommerce', 'Flutter', 'GA4 · GTM'],
                ],
                [
                    'name' => 'Silvia',
                    'role' => __('Content Strategy · Brand'),
                    'bio' => __('Costruisce i contenuti e il posizionamento editoriale a partire dai dati di campagna — sapendo quali messaggi funzionano nella ricerca a pagamento, quali touchpoint del funnel hanno più frizione, quali angoli di comunicazione sono già saturi. Il piano editoriale non è separato dalla strategia di marketing: nasce da essa.'),
                    'image' => '/images/silvia.webp',
                    'tags' => ['Content strategy', 'SEO', 'Piano editoriale', 'Email marketing', 'Brand positioning'],
                ],
            ],
        ]);
    }
}
