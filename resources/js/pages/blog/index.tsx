import { useCallback, useEffect, useRef, useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import ArticleCard from '@/components/articleCard';
import BordersDecorations from '@/components/bordersDecorations';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
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
    const filtersNavRef = useRef<HTMLElement>(null);
    const [canScrollLeft, setCanScrollLeft] = useState(false);
    const [canScrollRight, setCanScrollRight] = useState(false);

    const updateScrollState = useCallback(() => {
        const nav = filtersNavRef.current;
        if (!nav) return;
        setCanScrollLeft(nav.scrollLeft > 1);
        setCanScrollRight(nav.scrollLeft + nav.clientWidth < nav.scrollWidth - 1);
    }, []);

    useEffect(() => {
        const nav = filtersNavRef.current;
        if (!nav) return;
        updateScrollState();
        nav.addEventListener('scroll', updateScrollState);
        return () => nav.removeEventListener('scroll', updateScrollState);
    }, [updateScrollState]);

    const scrollByOneTag = (direction: 'left' | 'right') => {
        const nav = filtersNavRef.current;
        if (!nav) return;
        const children = Array.from(nav.children) as HTMLElement[];
        const navLeft = nav.getBoundingClientRect().left;

        if (direction === 'right') {
            const next = children.find((child) => child.getBoundingClientRect().left > navLeft + 1);
            if (next) nav.scrollTo({ left: nav.scrollLeft + (next.getBoundingClientRect().left - navLeft), behavior: 'smooth' });
        } else {
            const prev = [...children].reverse().find((child) => child.getBoundingClientRect().left < navLeft - 1);
            if (prev) nav.scrollTo({ left: nav.scrollLeft + (prev.getBoundingClientRect().left - navLeft), behavior: 'smooth' });
        }
    };

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
                <div className="px-4">
                    <p className="kicker">Insights & Risorse</p>
                    <h1 className="page__title">In evidenza</h1>
                    <p className="mt-4 text-lg text-mercury-500">Articoli, guide e approfondimenti su sviluppo web, design e tecnologia.</p>
                </div>{' '}
            </header>

            {/* Featured Articles */}
            {featuredArticles.length > 0 && (
                <section className="container mb-16">
                    <div className="blog-index__featured-grid">
                        <BordersDecorations />
                        {featuredArticles.map((article) => (
                            <ArticleCard key={article.slug} article={article} className="bg-white" isLandscape={true} />
                        ))}
                    </div>
                </section>
            )}

            {/* Articles Grid */}
            {articles.data.length > 0 && (
                <section className="container mb-16">
                    <div className="mb-12 flex flex-col justify-between px-4 md:flex-row md:items-end">
                        <div>
                            <p className="kicker">Ultimi articoli</p>
                            <h2 className="section__title">Tutti gli articoli</h2>
                        </div>
                        {/* Category Filters */}
                        <div className="blog-index__filters">
                            <nav ref={filtersNavRef} className="blog-index__filters-nav">
                                <Link
                                    href={index.url()}
                                    preserveState
                                    className={`blog-index__filter ${!currentCategory ? 'blog-index__filter--active' : ''}`}
                                >
                                    Tutti
                                </Link>
                                {categories.map((category) => (
                                    <Link
                                        key={category.slug}
                                        href={index.url({ query: { category: category.slug } })}
                                        preserveState
                                        className={`blog-index__filter ${currentCategory === category.slug ? 'blog-index__filter--active' : ''}`}
                                    >
                                        {category.name}
                                    </Link>
                                ))}
                            </nav>
                            <div className="flex justify-end gap-4 mt-2">
                                <button type="button" onClick={() => scrollByOneTag('left')} disabled={!canScrollLeft} aria-label="Scorri filtri a sinistra" className="disabled:opacity-25 transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="currentColor" className="size-3">
                                        <polygon points="10,0 10,10 0,5" />
                                    </svg>
                                </button>
                                <button type="button" onClick={() => scrollByOneTag('right')} disabled={!canScrollRight} aria-label="Scorri filtri a destra" className="disabled:opacity-25 transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 10 10" fill="currentColor" className="size-3">
                                        <polygon points="0,0 0,10 10,5" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div className="blog-index__grid">
                        {articles.data.map((article) => (
                            <ArticleCard
                                key={article.slug}
                                article={article}
                                className={`border-y border-mercury-200 first:border-l! last:border-r! odd:border-l even:border-l md:even:border-l-0 lg:odd:border-l-0 lg:nth-[4]:border-l`}
                            />
                        ))}
                    </div>
                </section>
            )}

            <div className="px-6">
                {/* Pagination */}
                {articles.last_page > 1 && (
                    <nav className="blog-index__pagination">
                        <div className="left__decoration" />
                        <div className="right__decoration" />
                        {articles.current_page > 1 && (
                            <Link href={articles.links[0].url!} preserveState className="blog-index__pagination_button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="-rotate-180">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </Link>
                        )}

                        <span className="blog-index__pagination-counter">
                            {articles.current_page} / {articles.last_page}
                        </span>

                        {articles.current_page < articles.last_page && (
                            <Link href={articles.links[articles.links.length - 1].url!} preserveState className="blog-index__pagination_button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </Link>
                        )}
                    </nav>
                )}
            </div>

            <Colophon />
        </>
    );
}
