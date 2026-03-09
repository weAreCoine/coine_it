<?php
declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Abstracts\AbstractPageController;
use App\Services\ClientsLogosService;
use Inertia\Inertia;
use Inertia\Response;

class ContentPageController extends AbstractPageController
{
    public function show(): Response
    {
        return Inertia::render('services/content', [
            'faqs' => [
                [
                    'question' => __('Quali tipi di contenuti create per le campagne di marketing?'),
                    'answer' => __('Creiamo una vasta gamma di contenuti per soddisfare ogni esigenza di marketing, tra cui articoli e blog post ottimizzati per SEO, testi pubblicitari per Google Ads, Facebook Ads e altre piattaforme, script per video promozionali, infografiche e design grafico, contenuti per email marketing. Tutto è studiato per massimizzare l\'efficacia delle tue campagne.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quanto tempo ci vuole per creare contenuti di qualità?'),
                    'answer' => __('Il tempo di creazione varia in base al tipo di contenuto e alla complessità del progetto. In generale, possiamo consegnare articoli e post di blog in pochi giorni, mentre contenuti più elaborati come video e infografiche possono richiedere da 1 a 2 settimane.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Come garantite che i contenuti siano ottimizzati per il marketing?'),
                    'answer' => __('Ogni contenuto che creiamo è basato su un\'analisi approfondita del target di riferimento e degli obiettivi di marketing. Usiamo le migliori pratiche di SEO, copywriting persuasivo e tecniche di ottimizzazione per garantire che i contenuti generino risultati misurabili, come un aumento di traffico, conversioni o engagement.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Offrite servizi di copywriting persuasivo?'),
                    'answer' => __('Sì, offriamo servizi di copywriting persuasivo per aiutarti a convertire i visitatori in clienti. I nostri copywriter sono esperti nella scrittura di testi che catturano l\'attenzione, stimolano l\'interesse e motivano all\'azione, ideali per landing page, email marketing e annunci pubblicitari.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Potete creare contenuti visivi come grafici e video?'),
                    'answer' => __('Assolutamente! Offriamo un servizio completo di creazione di contenuti visivi, tra cui design grafico per campagne pubblicitarie, infografiche informative, video promozionali e animazioni. Collaboriamo con te per sviluppare contenuti visivi accattivanti che trasmettono efficacemente il tuo messaggio.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Come posso garantire che i contenuti riflettano il tono e lo stile del mio brand?'),
                    'answer' => __('Prima di iniziare a creare contenuti, dedichiamo del tempo a capire il tono, lo stile e i valori del tuo brand. Lavoriamo a stretto contatto con te durante tutto il processo per garantire che i contenuti siano in linea con la tua identità di marca. Puoi fornire linee guida o esempi, e ti forniremo bozze per la tua approvazione.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Fornite servizi di ottimizzazione SEO per i contenuti?'),
                    'answer' => __('Sì, ogni contenuto testuale che creiamo è ottimizzato per la SEO, con particolare attenzione all\'utilizzo di parole chiave rilevanti, titoli e meta descrizioni efficaci, struttura dei contenuti e altre best practice SEO per migliorare il posizionamento nei motori di ricerca.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Quali informazioni vi servono per iniziare a creare contenuti?'),
                    'answer' => __('Per iniziare, abbiamo bisogno di una chiara comprensione dei tuoi obiettivi di marketing, del target di riferimento e del tono di voce del tuo brand. Ti chiederemo anche informazioni sulle parole chiave SEO, eventuali linee guida del marchio e altri dettagli rilevanti per creare contenuti su misura per il tuo business.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Posso fare modifiche ai contenuti una volta consegnati?'),
                    'answer' => __('Sì, offriamo un numero limitato di revisioni per garantire che i contenuti soddisfino pienamente le tue aspettative. Le revisioni sono incluse nel costo del servizio, e lavoriamo con te fino a quando non sei completamente soddisfatto del risultato finale.'),
                    'opened' => false,
                ],
                [
                    'question' => __('I contenuti che create sono originali?'),
                    'answer' => __('Assolutamente. Tutti i contenuti che produciamo sono 100% originali e creati su misura per le tue esigenze di marketing. Utilizziamo strumenti di controllo antiplagio per garantire che ogni pezzo sia unico e non copiato.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Offrite servizi di creazione di contenuti anche in altre lingue?'),
                    'answer' => __('Sì, offriamo la creazione di contenuti multilingua per aiutarti a raggiungere mercati internazionali. Abbiamo esperienza nella scrittura di contenuti in italiano, inglese, francese, tedesco e altre lingue, per assicurare che il tuo messaggio sia efficace ovunque tu voglia promuovere il tuo business.'),
                    'opened' => false,
                ],
                [
                    'question' => __('Posso usare i contenuti che create su più piattaforme?'),
                    'answer' => __('Sì, i contenuti che creiamo sono progettati per essere versatili e adattabili a diverse piattaforme. Puoi utilizzare i testi e i materiali visivi per il tuo sito web, social media, campagne pubblicitarie, email marketing e altro ancora. Creiamo contenuti scalabili per massimizzare la loro efficacia su tutti i canali.'),
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
                'title' => __('Contenuti che costruiscono fiducia'),
                'subtitle' => __('Il content marketing non serve solo a generare traffico. Serve a rispondere ai dubbi, creare autorevolezza e supportare concretamente il percorso d’acquisto.'),
                'columns' => [
                    [
                        'icon' => $this->loadSvg('feature-3.svg'),
                        'title' => 'Ricerca e pianificazione',
                        'description' => 'Identifichiamo temi e formati strategici per il tuo pubblico, trasformando la comunicazione in uno strumento per aumentare la qualità delle interazioni.',
                    ],
                    [
                        'icon' => $this->loadSvg('feature-1.svg'),
                        'title' => 'Creazione dei contenuti',
                        'description' => 'Scriviamo articoli, gestiamo i tuoi canali social e produciamo contenuti multimediali studiati per coinvolgere il tuo target.',
                    ],
                    [
                        'icon' => $this->loadSvg('feature-2.svg'),
                        'title' => 'Distribuzione e analisi',
                        'description' => 'Pubblichiamo dove conta davvero e analizziamo i risultati per ottimizzare la strategia giorno dopo giorno.',
                    ],
                ],
            ],
        ]);
    }
}
