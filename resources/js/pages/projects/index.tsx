import { Head, Link } from '@inertiajs/react';
import { index } from '@/actions/App/Http/Controllers/Pages/ProjectPageController';
import ProjectCardComponent from '@/components/projectCard';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import BordersDecorations from '@/components/bordersDecorations';
import ProjectCard = App.Entities.ProjectCard;
import ProjectCategoryItem = App.Entities.ProjectCategoryItem;

type PaginatedProjects = {
    data: ProjectCard[];
    current_page: number;
    last_page: number;
    links: { url: string | null; label: string; active: boolean }[];
};

type ProjectsIndexProps = {
    featuredProjects: ProjectCard[];
    projects: PaginatedProjects;
    categories: ProjectCategoryItem[];
    currentCategory: string | null;
    seoTitle: string;
    seoDescription: string;
    canonicalUrl: string;
};

export default function Index({ featuredProjects, projects, categories, currentCategory, seoTitle, seoDescription, canonicalUrl }: ProjectsIndexProps) {
    return (
        <>
            <Head title={seoTitle}>
                <meta name="description" content={seoDescription} />
                <link rel="canonical" href={canonicalUrl} />
                <meta property="og:title" content={seoTitle} />
                <meta property="og:description" content={seoDescription} />
                <meta property="og:url" content={canonicalUrl} />
            </Head>

            <Navigation />

            {/* Hero */}
            <header className="container mt-16 mb-12 text-balance">
                <div className="px-4">
                    <p className="kicker">Portfolio & Case Study</p>
                    <h1 className="page__title">I nostri progetti</h1>
                    <p className="mt-4 text-lg text-mercury-500">Scopri i progetti che abbiamo realizzato: obiettivi, strumenti e risultati.</p>
                </div>{' '}
            </header>

            {/* Featured Projects */}
            {featuredProjects.length > 0 && (
                <section className="container mb-16">
                    <div className="blog-index__featured-grid">
                        <BordersDecorations />
                        {featuredProjects.map((project) => (
                            <ProjectCardComponent key={project.slug} project={project} className="bg-white" isLandscape={true} />
                        ))}
                    </div>
                </section>
            )}

            {/* Projects Grid */}
            {projects.data.length > 0 && (
                <section className="container mb-16">
                    <div className="mb-12 flex flex-col justify-between px-4 md:flex-row md:items-end">
                        <div>
                            <p className="kicker">Ultimi progetti</p>
                            <h2 className="section__title">Tutti i progetti</h2>
                        </div>
                        {/* Category Filters */}
                        <div className="blog-index__filters">
                            <nav className="blog-index__filters-nav">
                                <Link
                                    href={index.url()}
                                    preserveState
                                    className={`blog-index__filter ${!currentCategory ? 'blog-index__filter--active' : ''}`}
                                >
                                    Tutti
                                </Link>
                                {categories.map((category) => (
                                    <Link
                                        key={category.slug}
                                        href={index.url({ query: { category: category.slug } })}
                                        preserveState
                                        className={`blog-index__filter ${currentCategory === category.slug ? 'blog-index__filter--active' : ''}`}
                                    >
                                        {category.name}
                                    </Link>
                                ))}
                            </nav>
                        </div>
                    </div>

                    <div className="blog-index__grid">
                        {projects.data.map((project) => (
                            <ProjectCardComponent
                                key={project.slug}
                                project={project}
                                className={`border-y border-mercury-200 first:border-l! last:border-r! odd:border-l even:border-l md:even:border-l-0 lg:odd:border-l-0 lg:nth-[4]:border-l`}
                            />
                        ))}
                    </div>
                </section>
            )}

            <div className="px-6">
                {/* Pagination */}
                {projects.last_page > 1 && (
                    <nav className="blog-index__pagination">
                        <div className="left__decoration" />
                        <div className="right__decoration" />
                        {projects.current_page > 1 && (
                            <Link href={projects.links[0].url!} preserveState className="blog-index__pagination_button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="-rotate-180">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </Link>
                        )}

                        <span className="blog-index__pagination-counter">
                            {projects.current_page} / {projects.last_page}
                        </span>

                        {projects.current_page < projects.last_page && (
                            <Link href={projects.links[projects.links.length - 1].url!} preserveState className="blog-index__pagination_button">
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
