import { Head, Link } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import Colophon from '@/components/colophon';
import ContactForm from '@/components/contactForm';
import ProjectCardComponent from '@/components/projectCard';
import { index } from '@/actions/App/Http/Controllers/Pages/ProjectPageController';
import ProjectCard = App.Entities.ProjectCard;

type ProjectPageProps = {
    title: string;
    slug: string;
    content: string;
    excerpt: string;
    cover: string | null;
    goal: string | null;
    tools: string[];
    results: string | null;
    categories: string[];
    tags: string[];
    authorName: string;
    createdAt: string;
    createdAtIso: string;
    seoTitle: string;
    seoDescription: string;
    seoImage: string | null;
    canonicalUrl: string;
    relatedProjects: ProjectCard[];
};

export default function Show({
    title,
    content,
    cover,
    goal,
    tools,
    results,
    categories,
    tags,
    authorName,
    createdAt,
    createdAtIso,
    seoTitle,
    seoDescription,
    seoImage,
    canonicalUrl,
    relatedProjects,
}: ProjectPageProps) {
    const jsonLd = {
        '@context': 'https://schema.org',
        '@type': 'CreativeWork',
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
                    <h1 className="text-center text-[2.5rem] font-medium">{title}</h1>
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

                {/* Goal, Tools, Results */}
                {(goal || tools.length > 0 || results) && (
                    <section className="container mx-auto mb-12 max-w-160">
                        <div className="grid gap-8 md:grid-cols-3">
                            {goal && (
                                <div>
                                    <h2 className="mb-2 text-sm font-semibold tracking-wider text-mercury-400 uppercase">Obiettivo</h2>
                                    <div className="rich__text" dangerouslySetInnerHTML={{ __html: goal }} />
                                </div>
                            )}
                            {tools.length > 0 && (
                                <div>
                                    <h2 className="mb-2 text-sm font-semibold tracking-wider text-mercury-400 uppercase">Strumenti</h2>
                                    <ul className="list-inside list-disc text-mercury-600">
                                        {tools.map((tool) => (
                                            <li key={tool}>{tool}</li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                            {results && (
                                <div>
                                    <h2 className="mb-2 text-sm font-semibold tracking-wider text-mercury-400 uppercase">Risultati</h2>
                                    <div className="rich__text" dangerouslySetInnerHTML={{ __html: results }} />
                                </div>
                            )}
                        </div>
                    </section>
                )}

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

            {/* Contact */}
            <section className="container my-32">
                <div className="mb-8 text-center">
                    <p className="kicker">Hai un progetto simile?</p>
                    <h2 className="section__title">Parliamone insieme</h2>
                    <p className="mx-auto mt-2 max-w-lg text-mercury-500">Raccontaci la tua idea e scopri come possiamo aiutarti a realizzarla.</p>
                </div>
                <ContactForm />
            </section>

            {/* Related Projects */}
            {relatedProjects.length > 0 && (
                <section className="container my-32">
                    <p className="kicker">Sullo stesso argomento</p>
                    <h2 className="section__title">Progetti correlati</h2>

                    <div className="mt-8 grid divide-y divide-mercury-200 border border-mercury-200 md:grid-cols-3 md:divide-x md:divide-y-0">
                        {relatedProjects.map((project) => (
                            <ProjectCardComponent key={project.slug} project={project} />
                        ))}
                    </div>

                    <div className="mt-8 flex justify-center">
                        <Link href={index.url()} className="button__primary reverse__motion">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="-rotate-180">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                            <span>Torna ai progetti</span>
                        </Link>
                    </div>
                </section>
            )}

            <Colophon />
        </>
    );
}
