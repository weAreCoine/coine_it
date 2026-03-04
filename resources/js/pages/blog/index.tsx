import { Head, Link } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import Colophon from '@/components/colophon';
import ArticleCard from '@/components/articleCard';
import FeaturedArticleCard from '@/components/featuredArticleCard';
import { index } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import BlogArticleCard = App.Entities.BlogArticleCard;
import BlogCategoryItem = App.Entities.BlogCategoryItem;

type PaginatedArticles = {
    data: BlogArticleCard[];
    current_page: number;
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
};

type BlogIndexProps = {
    featuredArticles: BlogArticleCard[];
    articles: PaginatedArticles;
    categories: BlogCategoryItem[];
    currentCategory: string | null;
    seoTitle: string;
    seoDescription: string;
    canonicalUrl: string;
};

export default function Index({ featuredArticles, articles, categories, currentCategory, seoTitle, seoDescription, canonicalUrl }: BlogIndexProps) {
    return (
        <>
            <Head title={seoTitle}>
                <meta name="description" content={seoDescription} />
                <link rel="canonical" href={canonicalUrl} />
                <meta property="og:title" content={seoTitle} />
                <meta property="og:description" content={seoDescription} />
                <meta property="og:url" content={canonicalUrl} />
            </Head>

            <Navigation />

            {/* Hero */}
            <header className="container mt-16 mb-12 text-balance">
                <p className="kicker">Insights & Risorse</p>
                <h1 className="page__title">Gli ultimi articoli</h1>
                <p className="mt-4 text-lg text-mercury-500">Articoli, guide e approfondimenti su sviluppo web, design e tecnologia.</p>
            </header>

            {/* Featured Articles */}
            {featuredArticles.length > 0 && (
                <section className="container mb-16">
                    <div className="mt-8 grid gap-6 md:grid-cols-2">
                        {featuredArticles.map((article) => (
                            <FeaturedArticleCard key={article.slug} article={article} />
                        ))}
                    </div>
                </section>
            )}

            {/* Articles Grid */}
            {articles.data.length > 0 && (
                <section className="container mb-16">
                    <div className="mb-12 flex items-end justify-between">
                        <div>
                            <p className="kicker">Ultimi articoli</p>
                            <h2 className="section__title">Tutti gli articoli</h2>
                        </div>
                        {/* Category Filters */}
                        <div className="scrollbar-hidden max-w-90 overflow-x-auto">
                            <nav className="flex snap-x snap-mandatory gap-2">
                                <Link
                                    href={index.url()}
                                    preserveState
                                    className={`snap-start snap-always px-4 py-2 text-sm font-medium uppercase transition-colors ${
                                        !currentCategory
                                            ? 'bg-black text-white'
                                            : 'border border-mercury-200 text-mercury-600 hover:border-mercury-400'
                                    }`}
                                >
                                    Tutti
                                </Link>
                                {categories.map((category) => (
                                    <Link
                                        key={category.slug}
                                        href={index.url({ query: { category: category.slug } })}
                                        preserveState
                                        className={`snap-start snap-always px-4 py-2 text-sm font-medium whitespace-nowrap uppercase transition-colors ${
                                            currentCategory === category.slug
                                                ? 'bg-black text-white'
                                                : 'border border-mercury-200 text-mercury-600 hover:border-mercury-400'
                                        }`}
                                    >
                                        {category.name}
                                    </Link>
                                ))}
                            </nav>
                        </div>
                    </div>

                    <div className="mt-8 grid divide-y divide-mercury-200 border border-mercury-200 md:grid-cols-3 md:divide-x">
                        {articles.data.map((article) => (
                            <ArticleCard key={article.slug} article={article} />
                        ))}
                    </div>
                </section>
            )}

            {/* Pagination */}
            {articles.last_page > 1 && (
                <nav className="container mb-32 flex items-center justify-center gap-4">
                    {articles.current_page > 1 ? (
                        <Link href={articles.links[0].url!} preserveState className="button__primary reverse__motion">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="-rotate-180">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </Link>
                    ) : (
                        <span className="inline-flex items-center justify-center bg-mercury-100 px-4 py-2 text-sm font-medium text-mercury-400 uppercase md:py-4">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                className="size-4 -rotate-180"
                            >
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    )}

                    <span className="text-sm font-medium">
                        {articles.current_page} / {articles.last_page}
                    </span>

                    {articles.current_page < articles.last_page ? (
                        <Link href={articles.links[articles.links.length - 1].url!} preserveState className="button__primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </Link>
                    ) : (
                        <span className="inline-flex items-center justify-center bg-mercury-100 px-4 py-2 text-sm font-medium text-mercury-400 uppercase md:py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="size-4">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </span>
                    )}
                </nav>
            )}

            <Colophon />
        </>
    );
}
