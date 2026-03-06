import { Head } from '@inertiajs/react';
import AnimatedLand from '@/components/animatedLand';
import BordersDecorations from '@/components/bordersDecorations';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import PrinciplesGrid from '@/components/sections/principlesGrid';

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
            <div className="container my-24">
                <div className="text-center">
                    <p className="kicker">Il nostro team</p>
                    <h2 className="section__title">Siamo Coiné: ci presentiamo</h2>
                    <p className="mx-auto max-w-lg text-balance">
                        Siamo un team dinamico che si costruisce su misura in base ai progetti che ci vengono affidati.
                    </p>
                </div>
                <div className="relative mt-8 grid gap-px bg-mercury-200 p-px lg:grid-cols-2">
                    <BordersDecorations />
                    <div className="flex items-center justify-start gap-8 bg-white p-4">
                        <div className="aspect-square max-w-40 shrink-0 lg:aspect-9/12 xl:aspect-square">
                            <img
                                src="https://cdn.prod.website-files.com/68a342b7066c56fa60eb3b39/68a6e5ff7b59be67bb3ac207_john-carter-avatar-quantum-webflow-template.jpg"
                                alt=""
                                className="h-full w-auto object-cover"
                            />
                        </div>
                        <div className="grow">
                            <div className="flex items-baseline gap-2">
                                <p className="text-xl font-semibold">Luca Barbi</p>
                                <p className="kicker text-xs">Sviluppo e Marketing</p>
                            </div>
                            <p>
                                Sviluppatore e performance marketer senior: lucido, diretto, concreto. Unisce visione tecnica e strategica per
                                costruire e ottimizzare progetti digitali senza giri di parole.
                            </p>
                        </div>
                    </div>
                    <div className="flex items-center justify-start gap-8 bg-white p-4">
                        <div className="aspect-square max-w-40 shrink-0 lg:aspect-9/12 xl:aspect-square">
                            <img
                                src="https://cdn.prod.website-files.com/68a342b7066c56fa60eb3b39/68a6e5d29b2f76acd088f052_sophie-moore-avatar-quantum-webflow-template.jpg"
                                alt=""
                                className="h-full w-auto object-cover"
                            />
                        </div>
                        <div className="grow">
                            <div className="flex items-baseline gap-2">
                                <p className="text-xl font-semibold">Silvia Pallai</p>
                                <p className="kicker text-xs">Content e Marketing</p>
                            </div>
                            <p>
                                Digital Strategist tra ascolto e direzione: mette in ordine le idee per costruire percorsi con senso e logica. Cura
                                parole e strategie social con sensibilità, trasformando la comprensione delle persone in visione concreta.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <Colophon />
        </>
    );
}
