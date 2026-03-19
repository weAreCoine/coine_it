import DevLabel from '@/components/devLabel';
import type { HeroPoint } from '@/types/dto/healthCheck';

type HealthCheckHeroProps = {
    points: HeroPoint[];
};

export default function HealthCheckHero({ points }: HealthCheckHeroProps) {
    return (
        <section id="health-check-hero" aria-labelledby="healthCheckHeroLabel" className="relative">
            <DevLabel name="HealthCheckHero" />
            <div className="container mt-20 grid grid-cols-1 items-center gap-12 md:grid-cols-2">
                <div>
                    <p className="kicker mb-2">Partner e-commerce</p>
                    <h1 id="healthCheckHeroLabel" className="page__title mb-4 text-balance">
                        Advertising, sviluppo e contenuti: <em>un unico flusso.</em>
                    </h1>
                    <p className="mb-8 max-w-lg text-balance text-mercury-500">
                        Lavoriamo con e-commerce che hanno bisogno di far funzionare advertising e infrastruttura tecnica senza coordinare fornitori
                        diversi che non si parlano.
                    </p>
                    <div className="flex flex-wrap items-center gap-4">
                        <a href="#health-check-quiz" className="button__primary">
                            <span>Analizza il tuo e-commerce</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div className="bg-mercury-50 p-4">
                    <ul className="space-y-0">
                        {points.map((point, index) => (
                            <li key={index} className="border-b border-mercury-200 py-4 last:border-b-0">
                                <p className="mb-1 font-medium">{point.title}</p>
                                <p className="text-sm text-mercury-500">{point.description}</p>
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </section>
    );
}
