<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Entities\BlogArticleCard;
use App\Entities\NavigationItem;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ClientsLogosService;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class WelcomePageController extends Controller
{

    public function show(): Response
    {
        return Inertia::render('welcome', [
            'hero' => $this->heroData(),
            'marquee' => $this->marqueeData(),
            'cardGrid' => $this->cardGridData(),
            'contentStats' => $this->contentStatsData(),
            'ctaBanner' => $this->ctaBannerData(),
            'articleGrid' => $this->articleGridData(),
            'tabSection' => $this->tabSectionData(),
        ]);
    }

    /**
     * @return array{title: string, description: string, link: NavigationItem}
     */
    private function heroData(): array
    {
        return [
            'title' => __('Costruiamo insieme la tua presenza digitale'),
            'description' => 'Aiutiamo gli e‑commerce a trasformare il marketing in un sistema misurabile lavorando su conversioni, vendite e marginalità.',
            'link' => new NavigationItem('Raccontaci la tua idea', route('contact.show')),
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem, slides: array<int, array{logoUrl: string, title: string, link: NavigationItem}>}
     */
    private function marqueeData(): array
    {
        return [
            'kicker' => __('Portfolio'),
            'title' => __('Dieci anni di successi: ecco alcuni amici cresciuti con noi.'),
            'subtitle' => __('Collaboriamo con realtà diverse accompagnandole nella crescita digitale, unendo tecnologia e marketing per costruire soluzioni coerenti con le reali esigenze del business.'),
            'link' => new NavigationItem('I nostri case studies', route('projects.index')),
            'slides' => ClientsLogosService::all()
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem, columns: list<array{icon: string, title: string, description: string}>}
     */
    private function cardGridData(): array
    {
        return [
            'kicker' => __('Il nostro metodo'),
            'title' => __('Decisioni basate sui dati.'),
            'subtitle' => __('Un metodo chiaro, basato su dati e conversioni.'),
            'link' => new NavigationItem('Raccontaci la tua idea', route('contact.show')),
            'columns' => [
                [
                    'icon' => $this->loadSvg('feature-1.svg'),
                    'title' => 'Analisi',
                    'description' => 'Definiamo obiettivi, target e priorità attraverso dati e valutazioni strategiche.',
                ],
                [
                    'icon' => $this->loadSvg('feature-2.svg'),
                    'title' => 'Implementazione',
                    'description' => 'Mettiamo in pratica la strategia con sviluppo, contenuti o campagne.',
                ],
                [
                    'icon' => $this->loadSvg('feature-3.svg'),
                    'title' => 'Monitoraggio',
                    'description' => 'Misuriamo e ottimizziamo costantemente le performance.',
                ],
            ],
        ];
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

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem, svg: string, skills: list<array{icon: string, scalar: string, description: string}>}
     */
    private function contentStatsData(): array
    {
        $skillIcon = <<<'SVG'
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
          <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
          <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
          <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
        </svg>
        SVG;

        return [
            'kicker' => __('Chi siamo'),
            'title' => __('Advertising e sviluppo e-commerce. Senza passaggi di consegna.'),
            'subtitle' => __('Professionisti senior su advertising, sviluppo e contenuti. Zero fornitori da coordinare, zero tempo perso in traduzioni a fare da interprete tra chi fa marketing e chi tocca il codice.'),
            'link' => new NavigationItem('Il nostro team', route('about')),
            'svg' => '',
            'skills' => [
                [
                    'icon' => $skillIcon,
                    'scalar' => '10+',
                    'description' => __('Anni di esperienza, dal fashion al biomedicale'),
                ],
                [
                    'icon' => $skillIcon,
                    'scalar' => '30+',
                    'description' => __('Brand gestiti, da PMI a Multinazionali'),
                ],
                [
                    'icon' => $skillIcon,
                    'scalar' => '500+',
                    'description' => __('Campagne advertising gestite e ottimizzate'),
                ],
            ],
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem}
     */
    private function ctaBannerData(): array
    {
        return [
            'kicker' => 'Approccio Data-Driven',
            'title' => 'Sviluppiamo strategie basate su dati concreti.',
            'subtitle' => 'Ogni strategia nasce dall\'analisi dei dati realmente utili. T aiutiamo a misurare ciò che conta e a interpretare correttamente i risultati.',
            'link' => new NavigationItem('Raccontaci la tua idea', route('contact.show')),
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem, articles: \Illuminate\Support\Collection<int, BlogArticleCard>}
     */
    private function articleGridData(): array
    {
        return [
            'kicker' => 'Blog',
            'title' => 'I nostri ultimi articoli',
            'subtitle' => 'Scopri le ultime strategie, trend e consigli per far crescere il tuo business digitale.',
            'articles' => Article::query()
                ->where('is_published', true)
                ->orderByDesc('created_at')
                ->with(['categories', 'user'])
                ->limit(2)
                ->get()
                ->map(fn(Article $article) => BlogArticleCard::fromArticle($article)),
            'link' => new NavigationItem('Sfoglia', route('blog.index')),
        ];
    }

    private function tabSectionData(): array
    {
        return [
            'kicker' => __('Servizi'),
            'title' => __('Cosa facciamo'),
            'subtitle' => __('Advertising, sviluppo e contenuti per e-commerce che vogliono vendere di più, non sembrare solamente più belli.'),
            'services' => [
                [
                    'tabIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 17 16" fill="none" class="squared-icon"><g clip-path="url(#clip0_15906_7025)"><path d="M16.1862 5.33333H10.1862V0L1.51953 10.6667H7.51953V16L16.1862 5.33333Z" fill="currentColor"></path></g></svg>',
                    'tabLabel' => __('Sviluppo'),
                    'icon' => asset('svg/flash.svg'),
                    'title' => __('Sviluppo'),
                    'link' => new NavigationItem('Scopri di più', route('service.developing')),
                    'html' => $this->renderPartial('partials.services.sviluppo'),
                ],
                [
                    'tabIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 17 16" fill="none" class="squared-icon"><path d="M8.8539 0.700195C7.69882 0.700195 6.56967 1.04272 5.60925 1.68445C4.64883 2.32618 3.90027 3.2383 3.45824 4.30546C3.0162 5.37263 2.90055 6.5469 3.12589 7.6798C3.35124 8.81269 3.90747 9.85332 4.72424 10.6701C5.54101 11.4869 6.58164 12.0431 7.71453 12.2684C8.84742 12.4938 10.0217 12.3781 11.0889 11.9361C12.156 11.4941 13.0681 10.7455 13.7099 9.78508C14.3516 8.82466 14.6941 7.69551 14.6941 6.54042C14.6924 4.99203 14.0765 3.50756 12.9816 2.41268C11.8868 1.3178 10.4023 0.701934 8.8539 0.700195Z" fill="currentColor"></path><path d="M9.58235 9.12158V12.8187H8.12229V9.12158L6.25707 7.87981L7.0674 6.66504L8.85232 7.85498L10.6372 6.66577L11.4476 7.88054L9.58235 9.12158Z" fill="white"></path><path d="M6.66406 14.5704H8.12412V15.3004H9.58418V14.5704H11.0442V13.1104H6.66406V14.5704Z" fill="currentColor"></path></svg>',
                    'tabLabel' => __('Marketing'),
                    'icon' => asset('svg/bulb.svg'),
                    'link' => new NavigationItem('Scopri di più', route('service.marketing')),
                    'title' => __('Marketing & Advertising'),
                    'html' => $this->renderPartial('partials.services.marketing'),
                ],
                [
                    'tabIcon' => '<svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 17 16" fill="none" class="squared-icon"><path d="M8.75722 0.972769C8.76758 0.856733 8.93715 0.856733 8.9475 0.972769C9.27623 4.65642 12.1958 7.57595 15.8794 7.90468C15.9955 7.91504 15.9955 8.08461 15.8794 8.09496C12.1958 8.42369 9.27623 11.3432 8.9475 15.0269C8.93715 15.1429 8.76758 15.1429 8.75722 15.0269C8.42849 11.3432 5.50896 8.42369 1.82531 8.09496C1.70927 8.08461 1.70927 7.91504 1.82531 7.90468C5.50896 7.57595 8.42849 4.65642 8.75722 0.972769Z" fill="currentColor"></path></svg>',
                    'tabLabel' => __('Contenuti'),
                    'icon' => asset('svg/star.svg'),
                    'link' => new NavigationItem('Scopri di più', route('service.content')),
                    'title' => __('Content'),
                    'html' => $this->renderPartial('partials.services.content'),
                ],
            ],
        ];
    }

    private function renderPartial(string $view): string
    {
        try {
            return view($view)->render();
        } catch (Throwable $exception) {
            ExceptionHandler::handle($exception);

            return '';
        }
    }
}
