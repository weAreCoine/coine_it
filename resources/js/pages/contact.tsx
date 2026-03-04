import { Head } from '@inertiajs/react';
import Colophon from '@/components/colophon';
import ContactForm from '@/components/contactForm';
import FaqAccordion from '@/components/faqAccordion';
import Navigation from '@/components/navigation';
import Faq = App.Entities.Faq;

type ContactPageProps = {
    faqs: Faq[];
};

export default function Contact({ faqs }: ContactPageProps) {
    return (
        <>
            <Head title="Contatti" />
            <Navigation />

            <div className="container mt-16 mb-32">
                <div className="mx-auto max-w-3xl text-center">
                    <p className="kicker">Raccontaci il tuo progetto e i tuoi obiettivi</p>
                    <h1 className="page__title my-2">Mettiamoci in contatto</h1>
                    <p className="mx-auto max-w-lg text-mercury-500">
                        Costruiamo basi digitali solide per supportare una crescita concreta e misurabile. Inizia oggi il tuo percorso di crescita
                        digitale.
                    </p>
                </div>

                <ContactForm />

                <div className="mx-auto mt-24 max-w-3xl">
                    <div className="grid grid-cols-1 gap-px bg-mercury-200 p-px md:grid-cols-3">
                        <div className="flex items-center gap-4 bg-white px-6 py-6">
                            <div className="inline-block shrink-0 bg-mercury-50 p-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    strokeWidth={1.5}
                                    stroke="currentColor"
                                    className="size-5"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p className="text-xs text-mercury-400">Sviluppo Web & App</p>
                                <a href="mailto:dev@coine.it" className="text-sm font-medium">
                                    dev@coine.it
                                </a>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 bg-white px-6 py-6">
                            <div className="inline-block shrink-0 bg-mercury-50 p-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    strokeWidth={1.5}
                                    stroke="currentColor"
                                    className="size-5"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p className="text-xs text-mercury-400">Marketing</p>
                                <a href="mailto:marketing@coine.it" className="text-sm font-medium">
                                    marketing@coine.it
                                </a>
                            </div>
                        </div>

                        <div className="flex items-center gap-4 bg-white px-6 py-6">
                            <div className="inline-block shrink-0 bg-mercury-50 p-3">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    strokeWidth={1.5}
                                    stroke="currentColor"
                                    className="size-5"
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p className="text-xs text-mercury-400">Sedi</p>
                                <p className="text-sm font-medium">Modena e Milano</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div className="bg-mercury-50 py-24">
                <div className="container max-w-3xl">
                    <p className="kicker text-center">FAQ</p>
                    <h2 className="section__title text-center">Domande frequenti</h2>
                    <p className="mb-12 text-center">Le cose che ci chiedono più spesso</p>
                    <FaqAccordion faqs={faqs} />
                </div>
            </div>

            <div></div>

            <Colophon />
        </>
    );
}
