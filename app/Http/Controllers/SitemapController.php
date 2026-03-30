<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Project;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke(): Sitemap
    {
        $sitemap = Sitemap::create();

        $this->addStaticPages($sitemap);
        $this->addArticlePages($sitemap);
        $this->addCategoryPages($sitemap);
        $this->addProjectPages($sitemap);

        return $sitemap;
    }

    private function addStaticPages(Sitemap $sitemap): void
    {
        $staticPages = [
            ['url' => '/', 'changeFreq' => Url::CHANGE_FREQUENCY_WEEKLY, 'priority' => 1.0],
            ['url' => '/chi-siamo', 'changeFreq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.8],
            ['url' => '/contact', 'changeFreq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.8],
            ['url' => '/servizi/sviluppo-app-siti-web', 'changeFreq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.9],
            ['url' => '/servizi/marketing', 'changeFreq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.9],
            ['url' => '/servizi/creazione-contenuti', 'changeFreq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.9],
            ['url' => '/progetti', 'changeFreq' => Url::CHANGE_FREQUENCY_WEEKLY, 'priority' => 0.8],
            ['url' => '/blog', 'changeFreq' => Url::CHANGE_FREQUENCY_DAILY, 'priority' => 0.9],
            ['url' => '/privacy-policy', 'changeFreq' => Url::CHANGE_FREQUENCY_YEARLY, 'priority' => 0.3],
            ['url' => '/cookie-policy', 'changeFreq' => Url::CHANGE_FREQUENCY_YEARLY, 'priority' => 0.3],
            ['url' => '/health-check', 'changeFreq' => Url::CHANGE_FREQUENCY_MONTHLY, 'priority' => 0.7],
        ];

        foreach ($staticPages as $page) {
            $sitemap->add(
                Url::create($page['url'])
                    ->setChangeFrequency($page['changeFreq'])
                    ->setPriority($page['priority'])
            );
        }
    }

    private function addArticlePages(Sitemap $sitemap): void
    {
        Article::query()
            ->where('is_published', true)
            ->select(['slug', 'updated_at'])
            ->each(function (Article $article) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/blog/{$article->slug}")
                        ->setLastModificationDate($article->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.7)
                );
            });
    }

    private function addCategoryPages(Sitemap $sitemap): void
    {
        Category::query()
            ->select(['slug', 'updated_at'])
            ->each(function (Category $category) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/blog/category/{$category->slug}")
                        ->setLastModificationDate($category->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                        ->setPriority(0.6)
                );
            });
    }

    private function addProjectPages(Sitemap $sitemap): void
    {
        Project::query()
            ->where('is_published', true)
            ->select(['slug', 'updated_at'])
            ->each(function (Project $project) use ($sitemap): void {
                $sitemap->add(
                    Url::create("/progetti/{$project->slug}")
                        ->setLastModificationDate($project->updated_at)
                        ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                        ->setPriority(0.7)
                );
            });
    }
}
