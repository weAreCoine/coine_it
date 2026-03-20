<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Services\ClientsLogosService;
use App\Services\HealthCheckConfig;
use Inertia\Inertia;
use Inertia\Response;

class HealthCheckPageController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('health-check', [
            'marquee' => [
                'kicker' => __('I nostri clienti'),
                'title' => __('Dieci anni di successi: ecco alcuni amici cresciuti con noi.'),
                'subtitle' => __('Collaboriamo con realtà diverse accompagnandole nella crescita digitale, unendo tecnologia e marketing per costruire soluzioni coerenti con le reali esigenze del business.'),
                'link' => null,
                'slides' => ClientsLogosService::all(),
            ],
            'heroPoints' => [
                ['text' => __('Tracking e dati: stai ottimizzando su segnali corretti?')],
                ['text' => __('Checkout mobile: dov\'è il punto di abbandono invisibile?')],
                ['text' => __('Campagne e sito: parlano la stessa lingua?')],
                ['text' => __('Advertising: il budget va dove rende di più?')],
            ],
            'steps' => [
                [
                    'number' => '01',
                    'title' => __('Rispondi alle domande'),
                    'description' => __('Sei domande sulla tua piattaforma, advertising, tracking, checkout mobile e maturità del progetto.'),
                ],
                [
                    'number' => '02',
                    'title' => __('Compila il form'),
                    'description' => __('Nome, email e URL del tuo e-commerce.'),
                ],
                [
                    'number' => '03',
                    'title' => __('Ricevi la diagnosi'),
                    'description' => __('Un documento personalizzato con punteggio, aree critiche e priorità di intervento immediato.'),
                ],
                [
                    'number' => '04',
                    'title' => __('Decidi il passo successivo'),
                    'description' => __('Puoi prenotare una call gratuita di 30 minuti. Se preferisci aspettare, ricevi contenuti specifici per la tua situazione.'),
                ],
            ],
            'faqs' => [
                [
                    'question' => __('L\'Health Check è davvero gratuito?'),
                    'answer' => __('Sì, senza costi e senza impegno. Ricevi un report con punteggio, aree critiche e priorità di intervento. Se vuoi approfondire, puoi prenotare una call gratuita di 30 minuti. Nessun obbligo.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Cosa ricevo se decido di fare la chiamata gratuita?'),
                    'answer' => __('30 minuti con Luca e Silvia in cui analizziamo insieme i dati del tuo e-commerce. Non quelli del quiz, quelli reali: posizionamento, performance del sito, ecc... Alla fine ricevi un documento con le 3 azioni a più alto impatto per la tua situazione specifica. Nessun pitch commerciale: se ha senso lavorare insieme te lo diciamo, altrimenti ti porti a casa il documento e lo usi come vuoi.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Per quali e-commerce è pensato?'),
                    'answer' => __('Per e-commerce attivi con un fatturato indicativo tra €50K e €1M annuo. Se hai un negozio online su WooCommerce, Shopify, PrestaShop o una piattaforma custom e stai investendo (o vuoi investire) in advertising, l\'analisi è calibrata sulla tua situazione.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Devo per forza prenotare una call dopo?'),
                    'answer' => __('No. Puoi leggere il primo report breve e fermarti lì. Se preferisci aspettare, riceverai via email contenuti mirati sulle aree emerse dall\'analisi. La call è un\'opzione, non un obbligo.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Chi analizza i miei risultati per preparare il report che consegnate in chiamata?'),
                    'answer' => __('Luca e Silvia, direttamente. Non un algoritmo, non un commerciale. Le stesse persone che gestiscono campagne e sviluppano e-commerce ogni giorno sono quelle che leggono le tue risposte e preparano eventuali approfondimenti.'),
                    'opened' => false,
                ],
            ],
            'questions' => HealthCheckConfig::questions(),
            'quizConfig' => HealthCheckConfig::quizConfig(),
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
                    'bio' => __('Costruisce i contenuti e il posizionamento editoriale a partire dai dati di campagna sapendo quali messaggi funzionano nella ricerca a pagamento, quali touchpoint del funnel hanno più frizione, quali angoli di comunicazione sono già saturi. Il piano editoriale non è separato dalla strategia di marketing: nasce da essa.'),
                    'image' => '/images/silvia.webp',
                    'tags' => ['Content strategy', 'SEO', 'Piano editoriale', 'Email marketing', 'Brand positioning'],
                ],
            ],
        ]);
    }
}
