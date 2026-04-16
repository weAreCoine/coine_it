import { Link } from '@inertiajs/react';
import { show } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import DevLabel from '@/components/devLabel';
import { cn } from '@/lib/utils';
import BlogArticleCard = App.Entities.BlogArticleCard;

type ArticleCardProps = {
    article: BlogArticleCard;
    className?: string;
    isLandscape?: boolean;
};

export default function ArticleCard({ article, className, isLandscape = false }: ArticleCardProps) {
    return (
        <Link href={show.url({ slug: article.slug })} prefetch="click" className={cn('group relative flex', className)}>
            <DevLabel name="ArticleCard" />
            <article className={'gap-2 ' + (isLandscape ? 'flex flex-col md:grid md:grid-cols-2 md:items-center' : 'flex flex-col')}>
                <figure className="mb-0 shrink-0 bg-mercury-50 p-4">
                    <img
                        src={article.cover ?? '/images/placeholder_image.webp'}
                        alt={article.title}
                        loading="lazy"
                        className="aspect-4/3 w-full object-cover"
                    />
                </figure>

                <div className="flex h-full flex-col justify-between">
                    <div className={`px-5 ${isLandscape && 'pt-5'}`}>
                        <h3 className="mb-2 text-2xl font-medium">{article.title}</h3>
                        <p className="mb-2">{article.excerpt}</p>
                        <time dateTime={article.createdAtIso} className="text-sm uppercase">
                            {article.createdAt}
                        </time>{' '}
                        <span className="text-sm font-semibold text-mercury-400">/</span>{' '}
                        {article.categories.map((category, index) => (
                            <span key={category.slug} className="text-sm uppercase">
                                {index > 0 && ', '}
                                {category.name}
                            </span>
                        ))}
                    </div>

                    <div className="flex justify-end">
                        <span className="relative flex aspect-square w-12 items-center justify-center overflow-hidden border-t border-l border-mercury-200 bg-mercury-50 lg:w-16">
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
                </div>
            </article>
        </Link>
    );
}
