import { Head, Link } from '@inertiajs/react';
import ArticleCard from '@/components/articleCard';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import BlogArticleCard = App.Entities.BlogArticleCard;

type PaginatedArticles = {
    data: BlogArticleCard[];
    current_page: number;
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
};

type CategoryPageProps = {
    name: string;
    slug: string;
    articles: PaginatedArticles;
};

export default function Category({ name, articles }: CategoryPageProps) {
    return (
        <>
            <Head title={name} />

            <Navigation />

            <header className="container mt-16 mb-12 text-balance">
                <div className="px-4">
                    <p className="kicker">Categoria</p>
                    <h1 className="page__title">{name}</h1>
                </div>
            </header>

            {articles.data.length > 0 ? (
                <section className="container mb-16">
                    <div className="blog-index__grid">
                        {articles.data.map((article) => (
                            <ArticleCard
                                key={article.slug}
                                article={article}
                                className="border-y border-mercury-200 first:border-l! last:border-r! odd:border-l even:border-l md:even:border-l-0 lg:odd:border-l-0 lg:nth-[4]:border-l"
                            />
                        ))}
                    </div>
                </section>
            ) : (
                <section className="container mb-16">
                    <p className="px-4 text-mercury-500">Nessun articolo in questa categoria.</p>
                </section>
            )}

            <div className="px-6">
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