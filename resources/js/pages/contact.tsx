import { Head, useForm } from '@inertiajs/react';
import React, { useState } from 'react';
import { store } from '@/actions/App/Http/Controllers/Pages/ContactPageController';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import { clsx } from 'clsx';
import Faq = App.Entities.Faq;

type ContactPageProps = {
    faqs: Faq[];
};

export default function Contact({ faqs }: ContactPageProps) {
    const [openFaqIndex, setOpenFaqIndex] = useState<number | null>(null);

    const { data, setData, post, processing, errors, reset, wasSuccessful } = useForm({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        message: '',
        termsAccepted: false,
    });

    function handleSubmit(e: React.SyntheticEvent<HTMLFormElement>) {
        e.preventDefault();
        post(store().url, {
            onSuccess: () => reset(),
        });
    }

    return (
        <>
            <Head title="Contatti" />
            <Navigation />

            <div className="container mt-16 mb-32">
                <div className="mx-auto max-w-3xl text-center">
                    <p className="kicker">Mettiamoci in contatto</p>
                    <h1 className="page__title my-2">Contattaci</h1>
                    <p className="mx-auto max-w-lg text-mercury-500">
                        Hai un progetto in mente o vuoi saperne di più sui nostri servizi? Compila il modulo e ti risponderemo al più presto.
                    </p>
                </div>

                {wasSuccessful && (
                    <div className="mx-auto mt-12 max-w-3xl border border-mercury-200 bg-mercury-50 px-6 py-4 text-center text-sm">
                        Grazie per averci contattato! Ti risponderemo al più presto.
                    </div>
                )}

                <form onSubmit={handleSubmit} className="mx-auto mt-16 max-w-3xl">
                    <div className="grid grid-cols-1 gap-px border border-mercury-200 bg-mercury-200 md:grid-cols-2">
                        <div className="coine__input">
                            <input
                                id="firstName"
                                type="text"
                                value={data.firstName}
                                onChange={(e) => setData('firstName', e.target.value)}
                                placeholder=" "
                                className=""
                            />
                            <label htmlFor="firstName">
                                Nome <span className="text-mercury-400">*</span>
                            </label>
                            {errors.firstName && <p className="error__message">{errors.firstName}</p>}
                        </div>

                        <div className="coine__input">
                            <input
                                id="lastName"
                                type="text"
                                value={data.lastName}
                                onChange={(e) => setData('lastName', e.target.value)}
                                placeholder=" "
                                className=""
                            />
                            <label htmlFor="lastName">
                                Cognome <span className="text-mercury-400">*</span>
                            </label>
                            {errors.lastName && <p className="error__message">{errors.lastName}</p>}
                        </div>

                        <div className="coine__input">
                            <input
                                id="email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                placeholder=" "
                                className=""
                            />
                            <label htmlFor="email">
                                Email <span className="text-mercury-400">*</span>
                            </label>
                            {errors.email && <p className="error__message">{errors.email}</p>}
                        </div>

                        <div className="coine__input">
                            <input
                                id="phone"
                                type="tel"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                                placeholder=" "
                                className=""
                            />
                            <label htmlFor="phone">Telefono</label>
                            {errors.phone && <p className="error__message">{errors.phone}</p>}
                        </div>

                        <div className="coine__input md:col-span-2">
                            <textarea
                                id="message"
                                rows={6}
                                value={data.message}
                                onChange={(e) => setData('message', e.target.value)}
                                placeholder=" "
                            />
                            <label htmlFor="message">
                                Raccontaci del tuo progetto... <span className="text-mercury-400">*</span>
                            </label>
                            {errors.message && <p className="error__message">{errors.message}</p>}
                        </div>
                    </div>
                    <div className="mt-8 block gap-6 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <label className="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    checked={data.termsAccepted}
                                    onChange={(e) => setData('termsAccepted', e.target.checked)}
                                    className="mt-0.5 size-4 shrink-0 accent-black"
                                />
                                <span className="text-sm text-mercury-500">
                                    Accetto il trattamento dei dati personali ai sensi del GDPR. <span className="text-mercury-400">*</span>
                                </span>
                            </label>
                            {errors.termsAccepted && <p className="error__message">{errors.termsAccepted}</p>}
                        </div>

                        <div className="mt-8 sm:mt-0">
                            <button type="submit" disabled={processing} className="button__primary">
                                {processing ? 'Invio in corso...' : 'Invia messaggio'}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>

                <div className="mx-auto mt-24 max-w-3xl">
                    <div className="grid grid-cols-1 gap-px border border-mercury-200 bg-mercury-200 md:grid-cols-3">
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

                <div className="mx-auto mt-24 max-w-3xl">
                    <p className="kicker text-center">FAQ</p>
                    <h2 className="section__title text-center">Domande frequenti</h2>
                    <p className="text-center">Le cose che ci chiedono più spesso</p>
                    <div className="mt-8 divide-y divide-mercury-200 border border-y border-mercury-200">
                        {faqs.map((faq, index) => (
                            <div key={index} className="p-4">
                                <button
                                    type="button"
                                    onClick={() => setOpenFaqIndex(openFaqIndex === index ? null : index)}
                                    className={clsx({
                                        'flex w-full cursor-pointer items-center justify-between py-5 text-left font-medium duration-300': true,
                                        'text-black': openFaqIndex === index,
                                        'text-mercury-400': openFaqIndex !== index,
                                    })}
                                >
                                    {faq.question}
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        strokeWidth={1.5}
                                        stroke="currentColor"
                                        className={`size-4 shrink-0 transition-transform duration-300 ${openFaqIndex === index ? 'rotate-180' : ''}`}
                                    >
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div
                                    className={`grid transition-[grid-template-rows] duration-300 ease-in-out ${openFaqIndex === index ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'}`}
                                >
                                    <div className="overflow-hidden">
                                        <p className="pb-5 text-sm text-mercury-500">{faq.answer}</p>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <Colophon />
        </>
    );
}
