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
            <Head title="E-commerce Health Check" />
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
