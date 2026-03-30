import { Head, Link } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/Pages/ArticlePageController';
import { show as showCategory } from '@/actions/App/Http/Controllers/Pages/CategoryPageController';
import ArticleCard from '@/components/articleCard';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import BlogArticleCard = App.Entities.BlogArticleCard;

type ArticlePageProps = {
    title: string;
    slug: string;
    content: string;
    excerpt: string;
    cover: string | null;
    categories: { name: string; slug: string }[];
    tags: { name: string; slug: string }[];
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
                    <div className="mb-4 flex items-center justify-center gap-4 text-center text-sm uppercase">
                        <div className="flex items-center justify-start gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-4">
                                <path
                                    fillRule="evenodd"
                                    d="M6.75 2.25A.75.75 0 0 1 7.5 3v1.5h9V3A.75.75 0 0 1 18 3v1.5h.75a3 3 0 0 1 3 3v11.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V7.5a3 3 0 0 1 3-3H6V3a.75.75 0 0 1 .75-.75Zm13.5 9a1.5 1.5 0 0 0-1.5-1.5H5.25a1.5 1.5 0 0 0-1.5 1.5v7.5a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5v-7.5Z"
                                    clipRule="evenodd"
                                />
                            </svg>

                            <time dateTime={createdAtIso}>{createdAt}</time>
                        </div>
                        {categories.length > 0 && (
                            <div className="flex items-center justify-start gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-4">
                                    <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
                                </svg>

                                {categories.map((cat, i) => (
                                    <span key={cat.slug}>
                                        <Link href={showCategory.url(cat.slug)} className="hover:underline">
                                            {cat.name}
                                        </Link>
                                        {i < categories.length - 1 && ', '}
                                    </span>
                                ))}
                            </div>
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
                        <ul className="flex flex-wrap items-center gap-2">
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-6">
                                    <path
                                        fillRule="evenodd"
                                        d="M5.25 2.25a3 3 0 0 0-3 3v4.318a3 3 0 0 0 .879 2.121l9.58 9.581c.92.92 2.39 1.186 3.548.428a18.849 18.849 0 0 0 5.441-5.44c.758-1.16.492-2.629-.428-3.548l-9.58-9.581a3 3 0 0 0-2.122-.879H5.25ZM6.375 7.5a1.125 1.125 0 1 0 0-2.25 1.125 1.125 0 0 0 0 2.25Z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                            </li>
                            {tags.map((tag) => (
                                <li key={tag.slug} className="rounded bg-mercury-100 px-3 py-1 text-sm">
                                    {tag.name.toLowerCase()}
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
