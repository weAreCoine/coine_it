import { Head, Link } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/Pages/ProjectPageController';
import Colophon from '@/components/colophon';
import ContactForm from '@/components/contactForm';
import Navigation from '@/components/navigation';
import ProjectCardComponent from '@/components/projectCard';
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
            <Head title={seoTitle} />

            <Navigation />

            <article>
                <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }} />

                {/* Hero / Header */}
                <header className="container mt-16 mb-8 max-w-180 text-balance">
                    <div className="mb-4 text-center text-sm uppercase">
                        <time className="hidden" dateTime={createdAtIso}>
                            {createdAt}
                        </time>
                        {categories.length > 0 && (
                            <>
                                <span className="mx-2 font-semibold text-mercury-200">/</span>
                                {categories.join(', ')}
                            </>
                        )}
                    </div>
                    <h1 className="page__title text-center">{title}</h1>
                    {/*<p className="mt-4 text-center text-mercury-500">{authorName}</p>*/}
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
                    <section className="mx-auto mb-12 max-w-200 text-sm">
                        <div className="relative grid grid-cols-2 gap-px bg-mercury-200 p-px">
                            {goal && (
                                <div className="bg-white p-4">
                                    <div className="mb-4 flex items-center gap-4 pb-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="size-6"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6"
                                            />
                                        </svg>
                                        <h2 className="text-base font-semibold tracking-wider">Obiettivi</h2>
                                    </div>
                                    <div className="rich__text" dangerouslySetInnerHTML={{ __html: goal }} />
                                </div>
                            )}
                            {results && (
                                <div className="bg-white p-4">
                                    <div className="mb-4 flex items-center gap-4 pb-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="size-6"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0"
                                            />
                                        </svg>

                                        <h2 className="text-base font-semibold tracking-wider">Risultati</h2>
                                    </div>
                                    <div className="rich__text" dangerouslySetInnerHTML={{ __html: results }} />
                                </div>
                            )}
                            {tools.length > 0 && (
                                <div className="col-span-2 bg-white p-4">
                                    <div className="mb-4 flex items-center gap-4 pb-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            strokeWidth={1.5}
                                            stroke="currentColor"
                                            className="size-6"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"
                                            />
                                        </svg>

                                        <h2 className="text-base font-semibold tracking-wider">Strumenti utilizzati</h2>
                                    </div>
                                    <ul className="project__tools_list">
                                        {tools.map((tool) => (
                                            <li key={tool}>{tool}</li>
                                        ))}
                                    </ul>
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
