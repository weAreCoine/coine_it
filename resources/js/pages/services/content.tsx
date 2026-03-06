import { Head } from '@inertiajs/react';
import AnimatedLand from '@/components/animatedLand';
import Colophon from '@/components/colophon';
import ContactForm from '@/components/contactForm';
import FaqAccordion from '@/components/faqAccordion';
import Navigation from '@/components/navigation';
import CardGrid from '@/components/sections/cardGrid';
import Marquee from '@/components/sections/marquee';
import type { CardGridData, MarqueeData } from '@/types/dto/sections';
import Faq = App.Entities.Faq;

export default function Content({ cardGrid, faqs, marquee }: { cardGrid: CardGridData; faqs: Faq[]; marquee: MarqueeData }) {
    return (
        <>
            <Head title="Content creation" />
            <Navigation />
            <div className="container mt-20 items-end justify-between md:flex">
                <div>
                    <p className="kicker mb-2">Servizi</p>
                    <h1 className="page__title text-balance">Content Marketing che funziona.</h1>
                </div>
                <div className="mt-2 max-w-md md:mt-0">
                    <p className="text-balance">Creiamo contenuti che hanno un ruolo preciso all’interno della strategia.</p>
                </div>
            </div>
            <AnimatedLand className="container mt-12" />
            <CardGrid {...cardGrid} />
            <Marquee {...marquee} />
            <div className="container my-24">
                <div className="mb-12 text-center">
                    <p className="kicker">FAQ</p>
                    <h2 className="section__title">Domande frequenti</h2>
                </div>
                <FaqAccordion faqs={faqs} bg={'bg-white'} />
            </div>
            <div className="my-24">
                <div className="mx-auto max-w-3xl text-center">
                    <p className="kicker">Non vediamo l'ora di conoscerti</p>
                    <h1 className="page__title my-2">Raccontaci il tuo progetto</h1>
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
