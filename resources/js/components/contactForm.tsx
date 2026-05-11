import { useForm, usePage } from '@inertiajs/react';
import React, { useEffect, useRef, useState } from 'react';
import { store } from '@/actions/App/Http/Controllers/ContactFormController';
import DevLabel from '@/components/devLabel';
import { trackMetaPixelEvent } from '@/hooks/useMetaPixel';
import { buildMetaUserData } from '@/lib/metaUserData';
import { generateUuid } from '@/lib/uuid';

export default function ContactForm() {
    const { consent, metaPixel } = usePage().props as {
        consent?: { marketing?: boolean };
        metaPixel?: { externalId?: string | null };
    };
    const { data, setData, post, processing, errors, reset, transform, wasSuccessful } = useForm({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        message: '',
        termsAccepted: false,
        newsletterOptIn: false,
    });

    const [dismissed, setDismissed] = useState(false);
    const successRef = useRef<HTMLDivElement>(null);
    const errorRef = useRef<HTMLDivElement>(null);

    const hasErrors = Object.keys(errors).length > 0;
    const showSuccess = wasSuccessful && !dismissed;

    useEffect(() => {
        if (showSuccess) {
            successRef.current?.scrollIntoView({ behavior: 'smooth', block: 'center' });
            successRef.current?.focus();
        }
    }, [showSuccess]);

    useEffect(() => {
        if (hasErrors && !processing) {
            errorRef.current?.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }, [errors, hasErrors, processing]);

    function handleSubmit(e: React.SyntheticEvent<HTMLFormElement>) {
        e.preventDefault();
        const metaEventId = generateUuid();
        setDismissed(false);

        transform((formData) => ({ ...formData, metaEventId }));

        const userData = buildMetaUserData({
            email: data.email,
            phone: data.phone,
            firstName: data.firstName,
            lastName: data.lastName,
            externalId: metaPixel?.externalId,
        });

        post(store().url, {
            onSuccess: () => {
                if (consent?.marketing) {
                    trackMetaPixelEvent('Lead', metaEventId, {}, false, userData);
                }
                reset();
            },
        });
    }

    function handleSendAnother() {
        setDismissed(true);
        reset();
    }

    return (
        <div id="contactForm" className="relative">
            <DevLabel name="ContactForm" />

            {showSuccess ? (
                <div
                    ref={successRef}
                    role="status"
                    aria-live="polite"
                    tabIndex={-1}
                    className="mx-auto mt-16 max-w-3xl border border-mercury-200 bg-mercury-50 px-8 py-12 text-center outline-none"
                >
                    <p className="kicker">Grazie!</p>
                    <h3 className="section__title my-2">Messaggio inviato</h3>
                    <p className="mx-auto max-w-lg text-mercury-500">
                        Abbiamo ricevuto la tua richiesta. Ti risponderemo entro 24 ore lavorative all&apos;indirizzo email che ci hai indicato.
                    </p>
                    <button type="button" onClick={handleSendAnother} className="button__primary mt-8">
                        Invia un altro messaggio
                    </button>
                </div>
            ) : (
                <form onSubmit={handleSubmit} className="mx-auto mt-16 max-w-3xl">
                    {hasErrors && (
                        <div
                            ref={errorRef}
                            role="alert"
                            aria-live="assertive"
                            className="mb-6 border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700"
                        >
                            <p className="font-semibold">Non siamo riusciti a inviare il messaggio.</p>
                            <ul className="mt-2 list-disc space-y-1 pl-5">
                                {Object.entries(errors).map(([field, message]) => (
                                    <li key={field}>{message as string}</li>
                                ))}
                            </ul>
                        </div>
                    )}

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
                            <textarea id="message" rows={6} value={data.message} onChange={(e) => setData('message', e.target.value)} placeholder=" " />
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
                                    Accetto il trattamento dei dati personali ai sensi del GDPR<span className="text-mercury-400">*</span>.
                                </span>
                            </label>
                            {errors.termsAccepted && <p className="error__message">{errors.termsAccepted}</p>}

                            <label className="mt-3 flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    checked={data.newsletterOptIn}
                                    onChange={(e) => setData('newsletterOptIn', e.target.checked)}
                                    className="mt-0.5 size-4 shrink-0 accent-black"
                                />
                                <span className="text-sm text-mercury-500">Desidero iscrivermi alla newsletter</span>
                            </label>
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
            )}
        </div>
    );
}
