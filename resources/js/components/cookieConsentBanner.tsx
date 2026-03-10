import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { getConsentFromCookie } from '@/hooks/useConsent';

function setCookie(name: string, value: string, days: number): void {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

export default function CookieConsentBanner() {
    const [visible, setVisible] = useState(false);
    const [showSettings, setShowSettings] = useState(false);
    const [marketing, setMarketing] = useState(false);

    function saveConsent(marketingConsent: boolean): void {
        const consent = JSON.stringify({ necessary: true, marketing: marketingConsent });
        setCookie('cookie_consent', consent, 365);
        setVisible(false);
        router.reload();
    }

    useEffect(() => {
        const consent = getConsentFromCookie();
        if (!consent.given) {
            setVisible(true);
        }

        const handleOpenSettings = () => {
            setVisible(true);
            setShowSettings(true);
        };

        window.addEventListener('open-consent-settings', handleOpenSettings);
        return () => window.removeEventListener('open-consent-settings', handleOpenSettings);
    }, []);

    if (!visible) {
        return null;
    }

    return (
        <div className="fixed inset-x-0 bottom-0 z-50 border-t border-mercury-700/60 bg-mercury-950 p-6 shadow-2xl">
            <div className="mx-auto max-w-4xl">
                <div className="flex flex-col gap-4">
                    <div>
                        <p className="text-sm text-mercury-300">
                            Utilizziamo cookie tecnici necessari al funzionamento del sito e, con il tuo consenso, cookie di marketing per analizzare
                            il traffico e migliorare la tua esperienza.
                        </p>
                    </div>

                    {showSettings && (
                        <div className="flex flex-col gap-3 rounded-lg border border-mercury-700/60 bg-mercury-900 p-4">
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-semibold text-mercury-200">Necessari</p>
                                    <p className="text-xs text-mercury-400">Sempre attivi, indispensabili per il funzionamento del sito.</p>
                                </div>
                                <div className="rounded-full bg-mercury-600 px-3 py-1 text-xs whitespace-nowrap text-mercury-200">Sempre attivi</div>
                            </div>
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-semibold text-mercury-200">Marketing</p>
                                    <p className="text-xs text-mercury-400">Utilizzati per misurare l'efficacia delle campagne pubblicitarie.</p>
                                </div>
                                <button
                                    type="button"
                                    role="switch"
                                    aria-checked={marketing}
                                    onClick={() => setMarketing(!marketing)}
                                    className={`relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors ${marketing ? 'bg-mercury-400' : 'bg-mercury-700'}`}
                                >
                                    <span
                                        className={`pointer-events-none inline-block size-5 rounded-full bg-white shadow-sm transition-transform ${marketing ? 'translate-x-5' : 'translate-x-0'}`}
                                    />
                                </button>
                            </div>
                        </div>
                    )}

                    <div className="flex flex-col items-center gap-3 sm:flex-row sm:justify-end">
                        {!showSettings && (
                            <button
                                type="button"
                                onClick={() => setShowSettings(true)}
                                className="text-sm text-mercury-400 underline underline-offset-2 transition-colors hover:text-mercury-200"
                            >
                                Impostazioni cookie
                            </button>
                        )}
                        {showSettings && (
                            <button
                                type="button"
                                onClick={() => saveConsent(marketing)}
                                className="rounded-lg border border-mercury-600 px-5 py-2 text-sm font-semibold text-mercury-200 transition-colors hover:bg-mercury-800"
                            >
                                Salva preferenze
                            </button>
                        )}
                        <div className="flex space-x-2">
                            <button
                                type="button"
                                onClick={() => saveConsent(false)}
                                className="cursor-pointer rounded-lg border border-mercury-600 px-5 py-2 text-sm font-semibold text-mercury-200 transition-colors hover:bg-mercury-800"
                            >
                                Solo necessari
                            </button>
                            <button
                                type="button"
                                onClick={() => saveConsent(true)}
                                className="rounded-lg cursor-pointer bg-mercury-200 px-5 py-2 text-sm font-semibold text-mercury-950 transition-colors hover:bg-white"
                            >
                                Accetta tutti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
