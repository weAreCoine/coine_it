import { Link } from '@inertiajs/react';
import { show } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import { show as showCategory } from '@/actions/App/Http/Controllers/Pages/CategoryPageController';
import type { BlogData } from '@/types/dto/sections';
import BlogArticleCard = App.Entities.BlogArticleCard;

export default function Blog(props: BlogData) {
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

            <div className="flex items-end justify-between">
                <div>
                    <p className="kicker">{props.kicker}</p>
                    <h2 id="blogTitle" className="section__title">
                        {props.title}
                    </h2>
                    <p>{props.subtitle}</p>
                </div>
                <div>
                    <a href={props.link.href} title={props.link.title} className="button__primary relative flex py-6 text-center">
                        <span>{props.link.title}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>

            <div className="mt-8 grid grid-cols-2 divide-x divide-mercury-200 border border-mercury-200">
                {props.articles.map((article: BlogArticleCard) => (
                    <Link href={show.url({ slug: article.slug })} key={article.slug} prefetch className="group flex">
                        <article className="flex w-full flex-col justify-between gap-2">
                            <div className="px-5 pt-5">
                                {article.cover && (
                                    <figure className="mb-6 bg-mercury-50">
                                        <img src={article.cover} alt={article.title} loading="lazy" />
                                    </figure>
                                )}
                                <h3 className="mb-2 text-2xl font-medium">{article.title}</h3>
                                <p className="mb-2">{article.excerpt}</p>
                                <time dateTime={article.createdAtIso} className="text-sm uppercase">
                                    {article.createdAt}
                                </time>{' '}
                                <span className="text-sm font-semibold text-mercury-400">/</span>{' '}
                                {article.categories.map((category, index) => (
                                    <span key={category.slug} className="uppercase">
                                        {index > 0 && ', '}
                                        <Link
                                            href={showCategory.url({ slug: category.slug })}
                                            className="text-sm underline decoration-mercury-300 underline-offset-2 transition-colors hover:decoration-black"
                                            onClick={(e) => e.stopPropagation()}
                                        >
                                            {category.name}
                                        </Link>
                                    </span>
                                ))}
                            </div>
                            <div className="flex justify-end">
                                <span className="relative flex aspect-square w-16 items-center justify-center overflow-hidden border-t border-l border-mercury-200 bg-mercury-50">
                                    <span className="absolute inset-0 -translate-x-full bg-black transition-transform duration-300 ease-out group-hover:translate-x-0" />
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="currentColor"
                                        className="relative size-4 transition-colors duration-300 group-hover:text-white"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </article>
                    </Link>
                ))}
            </div>
        </section>
    );
}
