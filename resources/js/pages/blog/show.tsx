import { Head, Link } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import Colophon from '@/components/colophon';
import ArticleCard from '@/components/articleCard';
import { index } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import BlogArticleCard = App.Entities.BlogArticleCard;

type ArticlePageProps = {
    title: string;
    slug: string;
    content: string;
    excerpt: string;
    cover: string | null;
    categories: string[];
    tags: string[];
    authorName: string;
    createdAt: string;
    createdAtIso: string;
    seoTitle: string;
    seoDescription: string;
    seoImage: string | null;
    canonicalUrl: string;
    relatedArticles: BlogArticleCard[];
};

export default function Show({
    title,
    content,
    cover,
    categories,
    tags,
    authorName,
    createdAt,
    createdAtIso,
    seoTitle,
    seoDescription,
    seoImage,
    canonicalUrl,
    relatedArticles,
}: ArticlePageProps) {
    const jsonLd = {
        '@context': 'https://schema.org',
        '@type': 'BlogPosting',
        headline: seoTitle,
        description: seoDescription,
        datePublished: createdAtIso,
        url: canonicalUrl,
        author: {
            '@type': 'Person',
            name: authorName,
        },
        ...(seoImage ? { image: seoImage } : {}),
    };

    return (
        <>
            <Head title={seoTitle}>
                <meta name="description" content={seoDescription} />
                <link rel="canonical" href={canonicalUrl} />
                <meta property="og:type" content="article" />
                <meta property="og:title" content={seoTitle} />
                <meta property="og:description" content={seoDescription} />
                <meta property="og:url" content={canonicalUrl} />
                {seoImage && <meta property="og:image" content={seoImage} />}
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content={seoTitle} />
                <meta name="twitter:description" content={seoDescription} />
                {seoImage && <meta name="twitter:image" content={seoImage} />}
            </Head>

            <Navigation />

            <article>
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />

                {/* Hero / Header */}
                <header className="container mt-16 mb-8 max-w-180 text-balance">
                    <div className="mb-4 text-center text-sm uppercase">
                        <time dateTime={createdAtIso}>{createdAt}</time>
                        {categories.length > 0 && (
                            <>
                                <span className="mx-2 font-semibold text-mercury-200">/</span>
                                {categories.join(', ')}
                            </>
                        )}
                    </div>
                    <h1 className="text-center text-4xl font-semibold">{title}</h1>
                    <p className="mt-4 text-center text-mercury-500">{authorName}</p>
                </header>

                {/* Cover image full-width */}
                <figure className="relative container mb-12">
                    <div className="absolute bottom-full left-0 z-[-1] -translate-x-1/4 translate-y-1/2 text-mercury-200">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 381 382" fill="none">
                                <rect x="0.509766" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="1" width="76" height="76" stroke="currentColor"></rect>
                            </svg>
                        </div>
                        <div className="absolute top-0 left-0 h-full w-full bg-radial from-transparent to-white to-80%"></div>
                    </div>
                    <img src={cover ?? '/images/placeholder_image.webp'} alt={title} className="w-full rounded" />
                </figure>

                {/* Content */}
                <div className="container">
                    <div className="rich__text mx-auto max-w-160" dangerouslySetInnerHTML={{ __html: content }} />
                </div>

                {/* Tags */}
                {tags.length > 0 && (
                    <footer className="container mx-auto mt-12 max-w-3xl border-t border-mercury-200 pt-6">
                        <ul className="flex flex-wrap gap-2">
                            {tags.map((tag) => (
                                <li key={tag} className="rounded bg-mercury-100 px-3 py-1 text-sm">
                                    {tag}
                                </li>
                            ))}
                        </ul>
                    </footer>
                )}
            </article>

            {/* Related Articles */}
            {relatedArticles.length > 0 && (
                <section className="container my-32">
                    <p className="kicker">Sullo stesso argomento</p>
                    <h2 className="section__title">Potrebbero interessarti</h2>

                    <div className="mt-8 grid divide-y divide-mercury-200 border border-mercury-200 md:grid-cols-3 md:divide-x md:divide-y-0">
                        {relatedArticles.map((article) => (
                            <ArticleCard key={article.slug} article={article} />
                        ))}
                    </div>

                    <div className="mt-8 flex justify-center">
                        <Link href={index.url()} className="button__primary reverse__motion">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="-rotate-180">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                            <span>Torna al blog</span>
                        </Link>
                    </div>
                </section>
            )}

            <Colophon />
        </>
    );
}
