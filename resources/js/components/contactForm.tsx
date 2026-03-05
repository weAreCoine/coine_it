import { useForm } from '@inertiajs/react';
import React from 'react';
import { store } from '@/actions/App/Http/Controllers/ContactFormController';
import DevLabel from '@/components/devLabel';

export default function ContactForm() {
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
        <div className="relative">
            <DevLabel name="ContactForm" />
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
        </div>
    );
}
