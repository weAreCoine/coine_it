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
                    <p className="kicker mb-2">Analisi gratuita</p>
                    <h1 id="healthCheckHeroLabel" className="page__title mb-4 text-balance">
                        Il tuo e-commerce <br />
                        <em>funziona davvero?</em>
                    </h1>
                    <p className="mb-8 max-w-lg text-balance text-mercury-500">
                        Un&apos;analisi gratuita in 6 domande: ricevi un report personalizzato con i punti critici del tuo e-commerce e le azioni
                        concrete per migliorare.
                    </p>
                    <div className="mb-8 space-y-3">
                        {points.map((point, index) => (
                            <div key={index} className="flex items-start gap-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    className="mt-0.5 h-5 w-5 shrink-0 text-green-500"
                                >
                                    <path
                                        fillRule="evenodd"
                                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z"
                                        clipRule="evenodd"
                                    />
                                </svg>
                                <span className="text-mercury-600">{point.text}</span>
                            </div>
                        ))}
                    </div>
                    <a href="#health-check-quiz" className="button__primary">
                        <span>Analizza il tuo e-commerce</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
                <div className="flex flex-col items-center">
                    <picture>
                        <source srcSet="/images/health-check-result.webp" type="image/webp" />
                        <img src="/images/health-check-result.png" alt="Esempio di report E-commerce Health Check" className="shadow-2xl" />
                    </picture>
                </div>
            </div>
        </section>
    );
}
