import { show } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import ArticleCard from '@/components/articleCard';
import type { ArticleGridData } from '@/types/dto/sections';
import BlogArticleCard = App.Entities.BlogArticleCard;

export default function ArticleGrid(props: ArticleGridData) {
    const jsonLd = {
        '@context': 'https://schema.org',
        '@type': 'ItemList',
        itemListElement: props.articles.map((article: BlogArticleCard, position: number) => ({
            '@type': 'ListItem',
            position: position + 1,
            item: {
                '@type': 'BlogPosting',
                headline: article.title,
                description: article.excerpt,
                datePublished: article.createdAtIso,
                url: show.url({ slug: article.slug }),
                author: {
                    '@type': 'Person',
                    name: article.authorName,
                },
                ...(article.cover ? { image: article.cover } : {}),
            },
        })),
    };

    return (
        <section aria-labelledby="blogTitle" className="container my-32">
            <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />

            <div className="flex flex-col items-start justify-between sm:flex-row sm:items-end gap-4">
                <div>
                    <p className="kicker">{props.kicker}</p>
                    <h2 id="blogTitle" className="section__title">
                        {props.title}
                    </h2>
                    <p>{props.subtitle}</p>
                </div>
                <div>
                    <a href={props.link.href} title={props.link.title} className="button__primary relative flex text-center md:py-6">
                        <span>{props.link.title}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>

            <div className="mt-8 grid divide-y divide-mercury-200 border border-mercury-200 md:grid-cols-2 md:divide-x md:divide-y-0">
                {props.articles.map((article: BlogArticleCard) => (
                    <ArticleCard key={article.slug} article={article} />
                ))}
            </div>
        </section>
    );
}
