import { Head } from '@inertiajs/react';
import AnimatedLand from '@/components/animatedLand';
import BordersDecorations from '@/components/bordersDecorations';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import PrinciplesGrid from '@/components/sections/principlesGrid';
import ContactForm from '@/components/contactForm';

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
                    <div className="group flex flex-col items-center justify-start gap-8 bg-white p-4 sm:flex-row">
                        <div className="aspect-square max-w-40 shrink-0 overflow-hidden lg:aspect-9/12 xl:aspect-square">
                            <img
                                src={'/images/luca.webp'}
                                alt="Luca Barbi"
                                className="h-full w-auto scale-100 object-cover brightness-125 grayscale duration-300 group-hover:scale-110 group-hover:brightness-100 group-hover:grayscale-0"
                            />
                        </div>
                        <div className="grow">
                            <div className="flex flex-col items-baseline gap-2 xs:flex-row">
                                <p className="text-xl font-semibold">Luca Barbi</p>
                                <p className="kicker text-xs">Sviluppo e Marketing</p>
                            </div>
                            <p>
                                Sviluppatore e performance marketer senior: lucido, diretto, concreto. Unisce visione tecnica e strategica per
                                costruire e ottimizzare progetti digitali senza giri di parole.
                            </p>
                        </div>
                    </div>
                    <div className="group flex flex-col items-center justify-start gap-8 bg-white p-4 sm:flex-row">
                        <div className="aspect-square max-w-40 shrink-0 overflow-hidden lg:aspect-9/12 xl:aspect-square">
                            <img
                                src={'/images/silvia.webp'}
                                alt="Silvia Pallai"
                                className="h-full w-auto scale-100 object-cover brightness-125 grayscale duration-300 group-hover:scale-110 group-hover:brightness-100 group-hover:grayscale-0"
                            />
                        </div>
                        <div className="grow">
                            <div className="flex flex-col items-baseline gap-2 xs:flex-row">
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
