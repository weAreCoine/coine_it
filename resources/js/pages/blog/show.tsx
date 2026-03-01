import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';

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

            <article className="container my-16">
                <script
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
                />

                <header className="mb-8">
                    {categories.length > 0 && (
                        <p className="kicker">{categories.join(', ')}</p>
                    )}
                    <h1 className="section__title">{title}</h1>
                    <div className="mt-4 flex items-center gap-4 text-sm text-gray-500">
                        <span>{authorName}</span>
                        <time dateTime={createdAtIso}>{createdAt}</time>
                    </div>
                </header>

                {cover && (
                    <figure className="mb-8">
                        <img src={cover} alt={title} className="w-full rounded" />
                    </figure>
                )}

                <div className="prose max-w-none" dangerouslySetInnerHTML={{ __html: content }} />

                {tags.length > 0 && (
                    <footer className="mt-8 border-t pt-4">
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
        </>
    );
}
