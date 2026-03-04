import { Link } from '@inertiajs/react';
import { show } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import BlogArticleCard = App.Entities.BlogArticleCard;

export default function FeaturedArticleCard({ article }: { article: BlogArticleCard }) {
    return (
        <Link href={show.url({ slug: article.slug })} prefetch className="group flex border border-mercury-200">
            <article className="flex w-full flex-col justify-between gap-2">
                <div className="p-5">
                    <figure className="mb-6 bg-mercury-50">
                        <img src={article.cover ?? '/images/placeholder_image.webp'} alt={article.title} loading="lazy" className="w-full" />
                    </figure>
                    <div className="mb-3 text-sm uppercase">
                        <time dateTime={article.createdAtIso}>{article.createdAt}</time>
                        {article.categories.length > 0 && (
                            <>
                                <span className="mx-2 font-semibold text-mercury-400">/</span>
                                {article.categories.map((category, index) => (
                                    <span key={category.slug}>
                                        {index > 0 && ', '}
                                        {category.name}
                                    </span>
                                ))}
                            </>
                        )}
                    </div>
                    <h3 className="mb-3 text-3xl font-medium">{article.title}</h3>
                    <p className="text-mercury-600 leading-relaxed">{article.excerpt}</p>
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
    );
}
