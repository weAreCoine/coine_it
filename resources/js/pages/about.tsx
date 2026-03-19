import { Head } from '@inertiajs/react';
import AnimatedLand from '@/components/animatedLand';
import Colophon from '@/components/colophon';
import ContactForm from '@/components/contactForm';
import Navigation from '@/components/navigation';
import PrinciplesGrid from '@/components/sections/principlesGrid';
import TeamSection from '@/components/sections/teamSection';
import type { TeamMember } from '@/types/dto/sections';

interface NumberItem {
    scalar: string;
    description: string;
}

interface PrincipleItem {
    image: string;
    title: string;
}

interface AboutProps {
    numbers: NumberItem[];
    principles: PrincipleItem[];
}

const teamMembers: TeamMember[] = [
    {
        name: 'Luca Barbi',
        role: 'Sviluppo e Marketing',
        bio: 'Sviluppatore e performance marketer senior: lucido, diretto, concreto. Unisce visione tecnica e strategica per costruire e ottimizzare progetti digitali senza giri di parole.',
        image: '/images/luca.webp',
        tags: ['Google Ads', 'Meta Ads', 'Laravel', 'WooCommerce', 'Flutter', 'GA4 · GTM'],
    },
    {
        name: 'Silvia Pallai',
        role: 'Content e Marketing',
        bio: 'Digital Strategist tra ascolto e direzione: mette in ordine le idee per costruire percorsi con senso e logica. Cura parole e strategie social con sensibilità, trasformando la comprensione delle persone in visione concreta.',
        image: '/images/silvia.webp',
        tags: ['Content strategy', 'SEO', 'Piano editoriale', 'Email marketing', 'Brand positioning'],
    },
];

export default function About({ numbers, principles }: AboutProps) {
    return (
        <>
            <Head title="Chi siamo" />
            <Navigation />
            <div className="container mt-20 items-end justify-between md:flex">
                <div>
                    <p className="kicker mb-2">Chi siamo</p>
                    <h1 className="page__title">We Are Coiné</h1>
                </div>
                <div className="mt-2 max-w-md md:mt-0">
                    <p className="text-balance">
                        Siamo un team di professionisti freelance specializzati in eCommerce, performance marketing e tecnologie digitali.
                    </p>
                </div>
            </div>
            <AnimatedLand className="container mt-12" />
            <div className="container mt-8 grid grid-cols-2 items-center gap-6 md:flex md:justify-between">
                {numbers &&
                    numbers.map((number, index) => (
                        <div key={index}>
                            <div className="flex items-center justify-start gap-2">
                                <p className="text-4xl font-semibold xs:text-5xl">{number.scalar}</p>
                                <div className="inline-flex items-center justify-center rounded-full bg-mercury-100 p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 15 14" fill="none" className="size-2 xs:size-3">
                                        <path d="M7.40431 13.0625L7.4043 2" stroke="currentColor" strokeWidth="1.5"></path>
                                        <path d="M13.8141 8.40703L7.40703 2L1 8.40703" stroke="currentColor" strokeWidth="1.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <p className="text-sm xs:text-base">{number.description}</p>
                        </div>
                    ))}
            </div>
            <PrinciplesGrid principles={principles} />
            <TeamSection
                kicker="Il nostro team"
                title="Siamo Coiné: ci presentiamo"
                subtitle="Siamo un team dinamico che si costruisce su misura in base ai progetti che ci vengono affidati."
                members={teamMembers}
            />
            <div className="container my-24">
                <div className="mx-auto max-w-3xl text-center">
                    <p className="kicker">Ora tocca a te presentarti</p>
                    <h1 className="page__title my-2">Non vediamo l'ora di conoscerti</h1>
                    <p className="mx-auto max-w-lg text-mercury-500">
                        Parlaci delle tue idee e dei tuoi obiettivi. Ti risponderemo entro 24 ore e capiremo insieme come potremo esserti utili.
                    </p>
                </div>

                <ContactForm />
            </div>

            <Colophon />
        </>
    );
}
