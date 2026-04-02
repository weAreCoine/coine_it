<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Abstracts\AbstractPageController;
use App\Services\ClientsLogosService;
use Inertia\Inertia;
use Inertia\Response;

class MarketingPageController extends AbstractPageController
{
    public function show(): Response
    {
        $seoTitle = __('Marketing e Advertising — Coiné');
        $seoDescription = __('Le campagne funzionano solo se inserite in un sistema chiaro. L\'obiettivo non è "fare pubblicità", ma verificare che ogni euro investito generi valore reale.');
        $canonicalUrl = route('service.marketing');

        return Inertia::render('services/marketing', [
            'faqs' => [
                [
                    'question' => __('Cos\'è l\'online advertising e come può aiutare il mio business?'),
                    'answer' => __('L\'online advertising consiste nella promozione di prodotti o servizi attraverso piattaforme digitali come Google Ads, Facebook Ads, Instagram e altre, per raggiungere target specifici e aumentare la visibilità.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quali piattaforme di advertising utilizzate?'),
                    'answer' => __('Le principali piattaforme su cui operiamo sono: Google Ads, Facebook, Instagram, LinkedIn, YouTube, e altre, a seconda delle necessità del cliente.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quanto budget devo investire in una campagna pubblicitaria?'),
                    'answer' => __('Il budget dipende dagli obiettivi, dal settore e dalla piattaforma scelta. Offriamo consulenza per ottimizzare l\'investimento pubblicitario in modo efficiente.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Come misurate il successo di una campagna di online advertising?'),
                    'answer' => __('Analizziamo le diverse metriche (KPI) come il ROI (ritorno sull\'investimento), il CTR (click-through rate), le conversioni e altre metriche personalizzate per ogni campagna.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quanto tempo ci vuole per vedere i risultati delle campagne?'),
                    'answer' => __('I primi risultati possono essere visibili già dopo pochi giorni dall\'avvio della campagna, una piena ottimizzazione può richiedere settimane di monitoraggio e aggiustamenti.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Posso fare pubblicità anche se ho un piccolo budget?'),
                    'answer' => __('Dipende sempre dall\'obiettivo. È possibile avviare campagne anche con budget ridotti, ottimizzando ogni centesimo per ottenere il massimo impatto dalle risorse disponibili.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Come scegliete il target per le mie campagne pubblicitarie?'),
                    'answer' => __('Il target viene scelto in base a una combinazione di analisi di mercato, dati demografici, interessi e comportamento online del pubblico, per raggiungere gli utenti più rilevanti.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Potete anticipare il budget per le campagne?'),
                    'answer' => __('Sì, anche se consigliamo e supportiamo l\'apertura di un tuo account. Tuttavia possiamo anticipare il budget e poi fatturare al cliente.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Avete esperienze in settori specifici?'),
                    'answer' => __('Abbiamo esperienze in settori specifici come e-commerce, servizi professionali, prodotti di consumo, ecc.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Gestite anche la creazione dei contenuti per gli annunci (testi, grafica, video)?'),
                    'answer' => __('Forniamo un servizio completo, inclusa la creazione di testi pubblicitari, design grafico, e video per ottimizzare le performance della campagna. Tuttavia se il cliente preferisce, può fornirci il materiale.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Come posso sapere se il mio investimento pubblicitario sta funzionando?'),
                    'answer' => __('Forniamo report dettagliati e regolari sull\'andamento delle campagne, mostrando le performance delle metriche chiave e consigliando ottimizzazioni costanti.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Potete gestire solo una campagna o devo sottoscrivere un contratto a lungo termine?'),
                    'answer' => __('La flessibilità è la nostra caratteristica. Offriamo sia la gestione di campagne singole che piani continuativi per un monitoraggio e ottimizzazione costante delle attività pubblicitarie.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Cosa significa ottimizzazione della campagna?'),
                    'answer' => __('Monitoriamo e analizziamo le campagne attive per migliorare continuamente le performance, regolando budget, targeting, e creatività per massimizzare i risultati.'),
                    'opened' => false,
                ],
            ],
            'marquee' => [
                'kicker' => __('Portfolio'),
                'title' => __('Dieci anni di successi: ecco alcuni amici cresciuti con noi.'),
                'subtitle' => __('Collaboriamo con realtà diverse accompagnandole nella crescita digitale, unendo tecnologia e marketing per costruire soluzioni coerenti con le reali esigenze del business.'),
                'link' => null,
                'slides' => ClientsLogosService::all(),
            ],
            'cardGrid' => [
                'kicker' => __('Il nostro metodo'),
                'title' => __('Advertising per guidare le scelte'),
                'subtitle' => __('Le campagne funzionano solo se inserite in un sistema chiaro. L\'obiettivo non è "fare pubblicità", ma verificare che ogni euro investito generi valore reale'),
                'columns' => [
                    [
                        'icon' => $this->loadSvg('feature-3.svg'),
                        'title' => 'Contesto e obiettivi',
                        'description' => "Analizziamo lo scenario aziendale per rendere il rendimento dell'investimento totalmente misurabile.",
                    ],
                    [
                        'icon' => $this->loadSvg('feature-1.svg'),
                        'title' => 'Strategia e dati di vendita',
                        'description' => 'Colleghiamo le campagne ai dati reali per rendere il percorso d’acquisto più fluido e intuitivo.',
                    ],
                    [
                        'icon' => $this->loadSvg('feature-2.svg'),
                        'title' => 'Operatività e reporting',
                        'description' => 'Ottimizzazione continua basata su evidenze, non su tentativi. Report chiari con i dati che servono per migliorare le performance.',
                    ],
                ],
            ],
        ])->withViewData([
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'canonicalUrl' => $canonicalUrl,
        ]);
    }
}
