<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Entities\NavigationItem;
use App\Exceptions\ExceptionHandler;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class WelcomePageController extends Controller
{
    private NavigationItem $callToAction;

    public function show(): Response
    {
        $this->callToAction = new NavigationItem('Scopri di più', null, isCallToAction: true);

        return Inertia::render('welcome', [
            'hero' => $this->heroData(),
            'slider' => $this->sliderData(),
            'features' => $this->featuresData(),
            'about' => $this->aboutData(),
            'getInTouch' => $this->getInTouchData(),
            'blog' => $this->blog()
        ]);
    }

    /**
     * @return array{title: string, description: string, link: NavigationItem}
     */
    private function heroData(): array
    {
        return [
            'title' => 'Costruiamo insieme la tua presenza digitale',
            'description' => 'Aiutiamo gli e‑commerce a trasformare il marketing in un sistema misurabile lavorando su conversioni, vendite e marginalità.',
            'link' => $this->callToAction,
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem, slides: array<int, array{logoUrl: string, title: string, link: NavigationItem}>}
     */
    private function sliderData(): array
    {
        return [
            'kicker' => __('Integrations'),
            'title' => __('AI engineered to integrate across every platform'),
            'subtitle' => __('Lorem ipsum dolor sit amet consectetur scelerisque quam dui dictumst suspendisse iaculis ac gravida venenatis mattis sed.'),
            'link' => $this->callToAction,
            'slides' => collect(File::files(public_path('images/clients')))
                ->filter(fn($file) => in_array($file->getExtension(), ['png', 'jpg', 'jpeg', 'svg', 'webp']))
                ->map(fn($file) => [
                    'logoUrl' => asset('images/clients/'.$file->getFilename()),
                    'title' => Str::headline($file->getFilenameWithoutExtension()),
                    'link' => $this->callToAction,
                ])
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem, columns: list<array{icon: string, title: string, description: string}>}
     */
    private function featuresData(): array
    {
        return [
            'kicker' => __('Core principles'),
            'title' => __('Architecting tomorrow\'s mind'),
            'subtitle' => __('We are a team of experts in digital marketing'),
            'link' => $this->callToAction,
            'columns' => [
                [
                    'icon' => $this->loadSvg('feature-1.svg'),
                    'title' => 'Generality',
                    'description' => 'Lorem ipsum dolor sit amet consectetur nec quuis suspendisse nulla amet viverra tortor.',
                ],
                [
                    'icon' => $this->loadSvg('feature-2.svg'),
                    'title' => 'Generality',
                    'description' => 'Lorem ipsum dolor sit amet consectetur nec quuis suspendisse nulla amet viverra tortor.',
                ],
                [
                    'icon' => $this->loadSvg('feature-3.svg'),
                    'title' => 'Generality',
                    'description' => 'Lorem ipsum dolor sit amet consectetur nec quuis suspendisse nulla amet viverra tortor.',
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
    private function aboutData(): array
    {
        $skillIcon = <<<'SVG'
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
              <path d="M11.7 2.805a.75.75 0 0 1 .6 0A60.65 60.65 0 0 1 22.83 8.72a.75.75 0 0 1-.231 1.337 49.948 49.948 0 0 0-9.902 3.912l-.003.002c-.114.06-.227.119-.34.18a.75.75 0 0 1-.707 0A50.88 50.88 0 0 0 7.5 12.173v-.224c0-.131.067-.248.172-.311a54.615 54.615 0 0 1 4.653-2.52.75.75 0 0 0-.65-1.352 56.123 56.123 0 0 0-4.78 2.589 1.858 1.858 0 0 0-.859 1.228 49.803 49.803 0 0 0-4.634-1.527.75.75 0 0 1-.231-1.337A60.653 60.653 0 0 1 11.7 2.805Z" />
              <path d="M13.06 15.473a48.45 48.45 0 0 1 7.666-3.282c.134 1.414.22 2.843.255 4.284a.75.75 0 0 1-.46.711 47.87 47.87 0 0 0-8.105 4.342.75.75 0 0 1-.832 0 47.87 47.87 0 0 0-8.104-4.342.75.75 0 0 1-.461-.71c.035-1.442.121-2.87.255-4.286.921.304 1.83.634 2.726.99v1.27a1.5 1.5 0 0 0-.14 2.508c-.09.38-.222.753-.397 1.11.452.213.901.434 1.346.66a6.727 6.727 0 0 0 .551-1.607 1.5 1.5 0 0 0 .14-2.67v-.645a48.549 48.549 0 0 1 3.44 1.667 2.25 2.25 0 0 0 2.12 0Z" />
              <path d="M4.462 19.462c.42-.419.753-.89 1-1.395.453.214.902.435 1.347.662a6.742 6.742 0 0 1-1.286 1.794.75.75 0 0 1-1.06-1.06Z" />
            </svg>
            SVG;

        return [
            'kicker' => __('Core principles'),
            'title' => __('Architecting tomorrow\'s mind'),
            'subtitle' => __('We are a team of experts in digital marketing'),
            'link' => $this->callToAction,
            'svg' => '',
            'skills' => [
                [
                    'icon' => $skillIcon,
                    'scalar' => '32+',
                    'description' => 'Accuracy in predictive algorithms',
                ],
                [
                    'icon' => $skillIcon,
                    'scalar' => '99,5%',
                    'description' => 'Accuracy in predictive algorithms',
                ],
                [
                    'icon' => $skillIcon,
                    'scalar' => '99,5%',
                    'description' => 'Accuracy in predictive algorithms',
                ],
            ],
        ];
    }

    /**
     * @return array{kicker: string, title: string, subtitle: string, link: NavigationItem}
     */
    private function getInTouchData(): array
    {
        return [
            'kicker' => 'Get in touch',
            'title' => 'Join our team that is shaping the next era of intelligence',
            'subtitle' => "Lorem ipsum dolor sit amet consectetur nec quis suspendisse nulla\namet viverra tortor pharetra mauris a maecenas habitant est mattis.",
            'link' => $this->callToAction,
        ];
    }

    private function blog(): array
    {
        return [
            'kicker' => 'Blog',
            'title' => 'Our latest articles',
            'subtitle' => 'Lorem ipsum dolor sit amet consectetur nec quis suspendisse nulla',
            'articles' => Article::where('is_published', true)
                ->orderBy('created_at')
                ->with('categories')
                ->limit(2)
                ->get()
                ->map(function (Article $article) {
                    return [
                        'title' => $article->title,
                        'excerpt' => $article->excerpt,
                        'cover' => asset('svg/dots.svg'),
                        'categories' => $article->categories->pluck('name')->implode(', '),
                        'created_at' => $article->created_at->format('d M Y'),
                    ];
                }),
            'link' => $this->callToAction,

        ];
    }
}
