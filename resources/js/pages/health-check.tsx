import { Head } from '@inertiajs/react';
import Colophon from '@/components/colophon';
import FaqAccordion from '@/components/faqAccordion';
import Navigation from '@/components/navigation';
import HealthCheckCta from '@/components/sections/healthCheckCta';
import HealthCheckHero from '@/components/sections/healthCheckHero';
import HealthCheckQuiz from '@/components/sections/healthCheckQuiz';
import HealthCheckTeam from '@/components/sections/healthCheckTeam';
import HowWeWork from '@/components/sections/howWeWork';
import Marquee from '@/components/sections/marquee';
import type { HeroPoint, QuizConfig, QuizQuestion, WorkStep } from '@/types/dto/healthCheck';
import type { MarqueeData, TeamMember } from '@/types/dto/sections';
import Faq = App.Entities.Faq;

interface HealthCheckProps {
    marquee: MarqueeData;
    heroPoints: HeroPoint[];
    steps: WorkStep[];
    faqs: Faq[];
    questions: QuizQuestion[];
    quizConfig: QuizConfig;
    teamMembers: TeamMember[];
}

export default function HealthCheck({ marquee, heroPoints, steps, faqs, questions, quizConfig, teamMembers }: HealthCheckProps) {
    return (
        <>
            <Head title="Il tuo e-commerce funziona davvero? | Health Check gratuito — Coiné">
                <meta name="description" content="Rispondi a 6 domande sul tuo e-commerce e ricevi un report con punteggio, aree critiche e priorità di intervento. Gratuito, senza impegno, in 2 minuti." />
                <meta property="og:type" content="website" />
                <meta property="og:title" content="Il tuo e-commerce funziona davvero? Scopri il punteggio." />
                <meta property="og:description" content="6 domande. Un report personalizzato con punteggio e priorità di intervento per il tuo e-commerce. Gratuito." />
                <meta property="og:url" content="https://coine.it/health-check" />
                <meta property="og:image" content="https://coine.it/images/health_check_banner.png" />
                <meta name="twitter:card" content="summary_large_image" />
                <meta name="twitter:title" content="Il tuo e-commerce funziona davvero? Scopri il punteggio." />
                <meta name="twitter:description" content="6 domande. Un report personalizzato con punteggio e priorità di intervento per il tuo e-commerce. Gratuito." />
                <meta name="twitter:image" content="https://coine.it/images/health_check_banner.png" />
            </Head>
            <Navigation />
            <HealthCheckHero points={heroPoints} />
            <Marquee {...marquee} />
            <HowWeWork steps={steps} />
            <HealthCheckQuiz questions={questions} config={quizConfig} />
            <HealthCheckTeam members={teamMembers} />
            <div className="container my-24">
                <div className="mb-12 text-center">
                    <p className="kicker">FAQ</p>
                    <h2 className="section__title">Domande frequenti</h2>
                </div>
                <FaqAccordion faqs={faqs} bg={'bg-white'} bordersDecorations={false} />
            </div>
            <HealthCheckCta />
            <Colophon marginTop={false} />
        </>
    );
}
