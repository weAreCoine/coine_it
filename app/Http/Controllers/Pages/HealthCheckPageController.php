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
                    'question' => __('Cosa ricevo esattamente alla fine?'),
                    'answer' => __('Un report personalizzato con un punteggio su 100, il confronto con la media degli e-commerce che fanno questa analisi, e 3 findings specifici sulle aree dove stai perdendo risultati (o dove sei già avanti). Non è un documento generico: cambia in base alle tue risposte.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Per quali e-commerce è pensato?'),
                    'answer' => __('Per e-commerce attivi con un fatturato indicativo tra €50K e €1M. Se hai un negozio online su WooCommerce, Shopify, PrestaShop o una piattaforma custom e stai investendo (o vuoi investire) in advertising, l\'analisi è calibrata sulla tua situazione.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Devo per forza prenotare una call dopo?'),
                    'answer' => __('No. Puoi leggere il report e fermarti lì. Se preferisci aspettare, riceverai via email contenuti mirati sulle aree emerse dall\'analisi. La call è un\'opzione, non un obbligo.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Chi analizza i miei risultati?'),
                    'answer' => __('Luca e Silvia, direttamente. Non un algoritmo, non un commerciale. Le stesse persone che gestiscono campagne e sviluppano e-commerce ogni giorno sono quelle che leggono le tue risposte e preparano eventuali approfondimenti.'),
                    'opened' => false,
                ],
            ],
            'questions' => [
                [
                    'id' => 1,
                    'text' => __('Qual è la tua piattaforma e-commerce?'),
                    'hint' => __('La piattaforma determina le possibilità di integrazione e le opzioni di ottimizzazione tecnica.'),
                    'key' => 'platform',
                    'scored' => false,
                    'weight' => 0,
                    'order' => 1,
                    'finding' => null,
                    'options' => [
                        ['label' => 'WooCommerce', 'value' => 'woocommerce', 'points' => 0],
                        ['label' => 'Shopify', 'value' => 'shopify', 'points' => 0],
                        ['label' => 'PrestaShop / Magento', 'value' => 'prestashop_magento', 'points' => 0],
                        ['label' => __('Sviluppo custom o altra piattaforma'), 'value' => 'custom', 'points' => 0],
                    ],
                ],
                [
                    'id' => 2,
                    'text' => __('Come gestisci l\'advertising in questo momento?'),
                    'hint' => __('La gestione delle campagne è spesso il primo punto dove si perde efficienza.'),
                    'key' => 'advertising',
                    'scored' => true,
                    'weight' => 12,
                    'order' => 2,
                    'finding' => [
                        'negative_title' => __('Advertising non ancora attivo'),
                        'negative_text' => __('Non stai ancora investendo in advertising. Non è un problema di per sé, ma significa che la crescita dipende interamente da canali che non controlli.'),
                        'positive_title' => __('Gestione advertising attiva'),
                        'positive_text' => __('Hai una gestione advertising attiva. Il passo successivo è assicurarti che ogni euro speso lavori in sinergia con quello che succede sul sito.'),
                        'threshold_max' => 3,
                        'threshold_min' => 9,
                    ],
                    'options' => [
                        ['label' => __('Non faccio ancora advertising'), 'value' => 'none', 'points' => 3],
                        ['label' => __('Gestisco le campagne internamente'), 'value' => 'internal', 'points' => 6],
                        ['label' => __('Ho un freelance o una piccola agenzia'), 'value' => 'freelance', 'points' => 9],
                        ['label' => __('Lavoro con un\'agenzia strutturata'), 'value' => 'agency', 'points' => 12],
                    ],
                ],
                [
                    'id' => 3,
                    'text' => __('Stai per lanciare una nuova campagna e hai bisogno di una modifica al sito. Come gestisci la situazione?'),
                    'hint' => __('La velocità di intervento sul sito è uno dei fattori più sottovalutati nella performance delle campagne.'),
                    'key' => 'coordination',
                    'scored' => true,
                    'weight' => 25,
                    'order' => 3,
                    'finding' => [
                        'negative_title' => __('Marketing e sviluppo disconnessi'),
                        'negative_text' => __('Campagne e sito vivono in due mondi separati. Ogni volta che una modifica al sito aspetta lo sviluppatore, le tue campagne girano su una pagina che non è ottimizzata e il budget paga il costo di quell\'attesa.'),
                        'positive_title' => __('Controllo sulla catena campagna→sito'),
                        'positive_text' => __('Hai il controllo sulla catena campagna→sito. Questa velocità di esecuzione è un vantaggio competitivo che la maggior parte degli e-commerce della tua dimensione non ha.'),
                        'threshold_max' => 7,
                        'threshold_min' => 17,
                    ],
                    'options' => [
                        [
                            'label' => __('Marketing e sviluppo sono gestiti da fornitori diversi che si coordinano poco'),
                            'value' => 'separate', 'points' => 0
                        ],
                        [
                            'label' => __('Contatto uno sviluppatore esterno e mi adatto ai suoi tempi'),
                            'value' => 'external', 'points' => 7
                        ],
                        [
                            'label' => __('Lo gestisco io, intervengo direttamente sul sito'), 'value' => 'self',
                            'points' => 15
                        ],
                        [
                            'label' => __('Ho un team/persona interna che interviene in tempi rapidi'),
                            'value' => 'internal', 'points' => 25
                        ],
                    ],
                ],
                [
                    'id' => 4,
                    'text' => __('Quanto ti fidi dei dati su cui basi le decisioni per il tuo e-commerce?'),
                    'hint' => __('Il 53% degli e-commerce ottimizza le campagne su dati incompleti o errati.'),
                    'key' => 'tracking',
                    'scored' => true,
                    'weight' => 25,
                    'order' => 4,
                    'finding' => [
                        'negative_title' => __('Dati non affidabili'),
                        'negative_text' => __('Stai prendendo decisioni senza una base dati affidabile. Ottimizzare le campagne con dati incompleti è come guidare di notte a fari spenti: puoi andare avanti, ma non sai cosa stai evitando o dove stai andando.'),
                        'positive_title' => __('Setup tracciamento sopra la media'),
                        'positive_text' => __('Il tuo setup di tracciamento è sopra la media. Questo ti dà un vantaggio reale: ogni decisione può partire dai numeri, non dalle sensazioni.'),
                        'threshold_max' => 8,
                        'threshold_min' => 17,
                    ],
                    'options' => [
                        [
                            'label' => __('Non ho un setup strutturato, vado spesso a intuito'), 'value' => 'none',
                            'points' => 0
                        ],
                        [
                            'label' => __('Ho GA4 configurato, ma non mi fido completamente dei numeri'),
                            'value' => 'basic', 'points' => 8
                        ],
                        [
                            'label' => __('Ho un setup discreto, anche se so che ci sono lacune'), 'value' => 'decent',
                            'points' => 17
                        ],
                        [
                            'label' => __('Setup completo: GA4, GTM, eventi e-commerce, pixel (e li uso davvero)'),
                            'value' => 'complete', 'points' => 25
                        ],
                    ],
                ],
                [
                    'id' => 5,
                    'text' => __('Se un cliente apre il tuo sito dallo smartphone e prova a comprare, come va?'),
                    'hint' => __('Oltre il 60% degli acquisti online avviene da smartphone. Il checkout mobile è spesso il punto di frizione più sottovalutato.'),
                    'key' => 'mobile',
                    'scored' => true,
                    'weight' => 20,
                    'order' => 5,
                    'finding' => [
                        'negative_title' => __('Esperienza mobile non ottimizzata'),
                        'negative_text' => __('Il 60% dei tuoi potenziali clienti compra da smartphone. Se il checkout mobile non è stato testato e ottimizzato, stai perdendo vendite ogni giorno e probabilmente non sai quante.'),
                        'positive_title' => __('Checkout mobile ottimizzato'),
                        'positive_text' => __('Un checkout mobile ottimizzato ti mette avanti rispetto alla maggior parte dei competitor. È un vantaggio diretto sul tasso di conversione.'),
                        'threshold_max' => 5,
                        'threshold_min' => 13,
                    ],
                    'options' => [
                        [
                            'label' => __('Non l\'ho mai testato seriamente, non saprei dire'), 'value' => 'unknown',
                            'points' => 0
                        ],
                        [
                            'label' => __('Lento o poco ottimizzato (lo so, è un problema)'), 'value' => 'slow',
                            'points' => 5
                        ],
                        [
                            'label' => __('Funziona, ma non l\'abbiamo ottimizzato in modo specifico'), 'value' => 'ok',
                            'points' => 13
                        ],
                        [
                            'label' => __('Ottimizzato: pochi step, veloce, pensato per il mobile'),
                            'value' => 'optimized', 'points' => 20
                        ],
                    ],
                ],
                [
                    'id' => 6,
                    'text' => __('Dopo il primo acquisto, cosa succede al tuo cliente?'),
                    'hint' => __('Acquisire un nuovo cliente costa 5-7 volte di più che fidelizzarne uno esistente. Eppure è l\'area più trascurata negli e-commerce sotto €1M.'),
                    'key' => 'retention',
                    'scored' => true,
                    'weight' => 18,
                    'order' => 6,
                    'finding' => [
                        'negative_title' => __('Nessuna retention automatizzata'),
                        'negative_text' => __('Dopo il primo acquisto, i tuoi clienti non ricevono nulla di automatizzato. Stai pagando per acquisirli e poi li lasci andare! Acquisire un nuovo cliente costa 5-7 volte di più che far tornare uno esistente.'),
                        'positive_title' => __('Flussi di retention attivi'),
                        'positive_text' => __('Hai flussi di retention attivi. Questo significa che non stai solo comprando traffico, stai costruendo un asset di clienti che tornano.'),
                        'threshold_max' => 6,
                        'threshold_min' => 14,
                    ],
                    'options' => [
                        [
                            'label' => __('Niente di automatizzato, ci penso manualmente quando posso'),
                            'value' => 'none', 'points' => 0
                        ],
                        [
                            'label' => __('Ho qualche email automatica ma non so quanto funzioni'), 'value' => 'basic',
                            'points' => 6
                        ],
                        [
                            'label' => __('Ho flussi attivi (benvenuto, carrello abbandonato, post-acquisto) e li monitoro'),
                            'value' => 'active', 'points' => 14
                        ],
                        [
                            'label' => __('Ho un sistema di retention strutturato con segmentazione e flussi multipli'),
                            'value' => 'advanced', 'points' => 18
                        ],
                    ],
                ],
            ],
            'quizConfig' => [
                'resultRanges' => [
                    [
                        'min' => 75,
                        'max' => 100,
                        'color' => '#22c55e',
                        'label' => __('Il tuo e-commerce ha basi solide'),
                        'message' => __('Il tuo e-commerce ha una struttura matura. Il potenziale di crescita è nell\'ottimizzazione fine: i dettagli che fanno la differenza tra un e-commerce che funziona e uno che scala.'),
                        'cta_text' => __('Vuoi scoprire le ottimizzazioni specifiche che possono portarti al livello successivo? In 30 minuti analizziamo insieme i dati del tuo e-commerce e ti consegniamo un report con le 3 azioni a più alto impatto.'),
                        'motivational_title' => __('Vuoi sapere dove si nasconde il prossimo salto di crescita?'),
                        'motivational_text' => __('Prenota un incontro gratuito di 30 minuti: analizzeremo il tuo e-commerce nel dettaglio e ti consegneremo un report con le ottimizzazioni a più alto impatto per la tua situazione. Se vuoi, anticipaci su cosa vorresti concentrarti: ci aiuta a preparare un\'analisi più mirata.'),
                        'open_field_placeholder' => __('Su cosa vorresti concentrarti?'),
                    ],
                    [
                        'min' => 50,
                        'max' => 74,
                        'color' => '#eab308',
                        'label' => __('Buone fondamenta, margini di crescita importanti'),
                        'message' => __('Hai costruito una base che funziona, ma ci sono aree specifiche dove stai lasciando risultati sul tavolo. Sono interventi concreti, non rivoluzioni ed è proprio dove si nasconde il fatturato inespresso.'),
                        'cta_text' => __('Abbiamo identificato dove si nasconde il tuo fatturato inespresso. In una call di 30 minuti ti mostriamo esattamente dove intervenire e ti consegniamo un report personalizzato con le priorità.'),
                        'motivational_title' => __('Vuoi trasformare questi margini in fatturato?'),
                        'motivational_text' => __('Prenota un incontro gratuito di 30 minuti: analizzeremo il tuo e-commerce e ti consegneremo un report personalizzato con le priorità di intervento partendo da quelle che producono risultati più velocemente. Se vuoi, raccontaci qual è la sfida che senti più urgente.'),
                        'open_field_placeholder' => __('Qual è la sfida che senti più urgente?'),
                    ],
                    [
                        'min' => 25,
                        'max' => 49,
                        'color' => '#f97316',
                        'label' => __('Ci sono leve che non stai usando'),
                        'message' => __('Il tuo e-commerce ha potenziale, ma alcune aree critiche stanno lavorando contro di te. La buona notizia: sono problemi comuni e risolvibili e sapere dove sono è già metà del lavoro.'),
                        'cta_text' => __('Le aree di intervento sono chiare. In 30 minuti ti consegniamo un report con la sequenza esatta di azioni per sbloccare i risultati partendo da quelle che fanno la differenza più velocemente.'),
                        'motivational_title' => __('Vuoi sapere da dove iniziare?'),
                        'motivational_text' => __('Prenota un incontro gratuito di 30 minuti: ti consegneremo un report con la sequenza di azioni concrete per il tuo e-commerce: niente teoria, solo quello che serve fare prima e quello che può aspettare. Se vuoi, raccontaci cosa ti frena di più in questo momento.'),
                        'open_field_placeholder' => __('Cosa ti frena di più in questo momento?'),
                    ],
                    [
                        'min' => 0,
                        'max' => 24,
                        'color' => '#ef4444',
                        'label' => __('Le basi da consolidare prima di scalare'),
                        'message' => __('Prima di investire di più in advertising o nuovi canali, serve consolidare le fondamenta. Non è un problema di ambizione: è una questione di sequenza. Investire ora senza queste basi significa disperdere budget.'),
                        'cta_text' => __('La buona notizia: sapere dove sei è il primo passo. In 30 minuti ti aiutiamo a definire le priorità e ti consegniamo una roadmap concreta per costruire le basi su cui far crescere il tuo e-commerce.'),
                        'motivational_title' => __('Il primo passo è sapere dove sei. Il secondo è avere un piano.'),
                        'motivational_text' => __('Prenota un incontro gratuito di 30 minuti: ti consegneremo una roadmap chiara con le priorità per il tuo e-commerce costruita sulla tua situazione reale, non su best practice generiche. Se vuoi, raccontaci cosa senti come il problema più urgente.'),
                        'open_field_placeholder' => __('Cosa senti come il problema più urgente?'),
                    ],
                ],
                'benchmarkScore' => 52,
                'benchmarkText' => __('Il punteggio medio degli e-commerce che fanno questa analisi è 52/100.'),
                'transitionMessages' => [
                    __('Capito. Questa informazione ci aiuta a calibrare l\'analisi.'),
                    __('Bene, andiamo avanti.'),
                    __('Ok, un quadro più chiaro.'),
                    __('Registrato. Proseguiamo.'),
                    __('Perfetto, ci siamo quasi.'),
                ],
                'fallbackFinding' => [
                    'title' => __('Ottimizzazione fine'),
                    'text' => __('Il tuo e-commerce ha basi solide su tutti i fronti. Il margine di crescita è nell\'ottimizzazione fine: A/B testing sistematico, segmentazione avanzata, e una strategia di scaling che non sacrifichi i margini.'),
                ],
                'openField' => [
                    'text' => __('Qual è la sfida più grande per il tuo e-commerce in questo momento?'),
                    'placeholder' => __('Racconta liberamente quello che senti come il freno principale alla crescita del tuo e-commerce.'),
                    'maxLength' => 500,
                ],
                'calendlyUrl' => config('services.calendly.url', 'https://calendly.com/coine'),
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
                    'bio' => __('Costruisce i contenuti e il posizionamento editoriale a partire dai dati di campagna sapendo quali messaggi funzionano nella ricerca a pagamento, quali touchpoint del funnel hanno più frizione, quali angoli di comunicazione sono già saturi. Il piano editoriale non è separato dalla strategia di marketing: nasce da essa.'),
                    'image' => '/images/silvia.webp',
                    'tags' => ['Content strategy', 'SEO', 'Piano editoriale', 'Email marketing', 'Brand positioning'],
                ],
            ],
        ]);
    }
}
