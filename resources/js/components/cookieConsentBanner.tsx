import { useEffect, useState } from 'react';
import { getConsentFromCookie } from '@/hooks/useConsent';

function setCookie(name: string, value: string, days: number): void {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

interface ToggleProps {
    label: string;
    description: string;
    checked: boolean;
    onToggle: () => void;
}

function ConsentToggle({ label, description, checked, onToggle }: ToggleProps) {
    return (
        <div className="flex items-center justify-between">
            <div>
                <p className="text-sm font-semibold text-mercury-200">{label}</p>
                <p className="text-xs text-mercury-400">{description}</p>
            </div>
            <button
                type="button"
                role="switch"
                aria-checked={checked}
                onClick={onToggle}
                className={`relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors ${checked ? 'bg-mercury-400' : 'bg-mercury-700'}`}
            >
                <span
                    className={`pointer-events-none inline-block size-5 rounded-full bg-white shadow-sm transition-transform ${checked ? 'translate-x-5' : 'translate-x-0'}`}
                />
            </button>
        </div>
    );
}

export default function CookieConsentBanner() {
    const [visible, setVisible] = useState(false);
    const [showSettings, setShowSettings] = useState(false);
    const [marketing, setMarketing] = useState(false);
    const [analytics, setAnalytics] = useState(false);

    function saveConsent({ marketing: marketingConsent, analytics: analyticsConsent }: { marketing: boolean; analytics: boolean }): void {
        const consent = JSON.stringify({
            necessary: true,
            marketing: marketingConsent,
            analytics: analyticsConsent,
        });
        setCookie('cookie_consent', consent, 365);
        setVisible(false);
        // Full reload required to inject server-side tracking script tags
        // (Meta Pixel, GA, etc.) that are gated by the consent cookie in
        // app.blade.php. Inertia's soft reload would not re-render Blade.
        window.location.reload();
    }

    useEffect(() => {
        const consent = getConsentFromCookie();
        if (!consent.given) {
            setVisible(true);
        } else {
            setMarketing(consent.marketing);
            setAnalytics(consent.analytics);
        }

        const handleOpenSettings = () => {
            const current = getConsentFromCookie();
            setMarketing(current.marketing);
            setAnalytics(current.analytics);
            setVisible(true);
            setShowSettings(true);
        };

        window.addEventListener('open-consent-settings', handleOpenSettings);
        return () => window.removeEventListener('open-consent-settings', handleOpenSettings);
    }, []);

    useEffect(() => {
        if (!visible) {
            return;
        }

        const previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        return () => {
            document.body.style.overflow = previousOverflow;
        };
    }, [visible]);

    if (!visible) {
        return null;
    }

    return (
        <>
            <div
                className="fixed inset-0 z-40 bg-mercury-950/70 backdrop-blur-sm"
                aria-hidden="true"
            />
            <div
                role="dialog"
                aria-modal="true"
                aria-label="Preferenze cookie"
                className="fixed inset-x-0 bottom-0 z-50 border-t border-mercury-700/60 bg-mercury-950 p-6 shadow-2xl"
            >
                <div className="mx-auto max-w-4xl">
                    <div className="flex flex-col gap-4">
                        <div>
                            <p className="text-sm text-mercury-300">
                                Utilizziamo cookie tecnici necessari al funzionamento del sito e, con il tuo consenso, cookie di marketing e di
                                analytics per misurare le campagne e analizzare l'utilizzo del sito.
                            </p>
                        </div>

                        {showSettings && (
                            <div className="flex flex-col gap-3 rounded-lg border border-mercury-700/60 bg-mercury-900 p-4">
                                <div className="flex items-center justify-between">
                                    <div>
                                        <p className="text-sm font-semibold text-mercury-200">Necessari</p>
                                        <p className="text-xs text-mercury-400">Sempre attivi, indispensabili per il funzionamento del sito.</p>
                                    </div>
                                    <div className="rounded-full bg-mercury-600 px-3 py-1 text-xs whitespace-nowrap text-mercury-200">
                                        Sempre attivi
                                    </div>
                                </div>
                                <ConsentToggle
                                    label="Marketing"
                                    description="Utilizzati per misurare l'efficacia delle campagne pubblicitarie."
                                    checked={marketing}
                                    onToggle={() => setMarketing(!marketing)}
                                />
                                <ConsentToggle
                                    label="Analytics"
                                    description="Cookie analitici per analizzare l'utilizzo del sito (es. session replay, heatmap)."
                                    checked={analytics}
                                    onToggle={() => setAnalytics(!analytics)}
                                />
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
                                    onClick={() => saveConsent({ marketing, analytics })}
                                    className="rounded-lg border border-mercury-600 px-5 py-2 text-sm font-semibold text-mercury-200 transition-colors hover:bg-mercury-800"
                                >
                                    Salva preferenze
                                </button>
                            )}
                            <div className="flex space-x-2">
                                <button
                                    type="button"
                                    onClick={() => saveConsent({ marketing: false, analytics: false })}
                                    className="cursor-pointer rounded-lg border border-mercury-600 px-5 py-2 text-sm font-semibold text-mercury-200 transition-colors hover:bg-mercury-800"
                                >
                                    Solo necessari
                                </button>
                                <button
                                    type="button"
                                    onClick={() => saveConsent({ marketing: true, analytics: true })}
                                    className="rounded-lg cursor-pointer bg-mercury-200 px-5 py-2 text-sm font-semibold text-mercury-950 transition-colors hover:bg-white"
                                >
                                    Accetta tutti
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
