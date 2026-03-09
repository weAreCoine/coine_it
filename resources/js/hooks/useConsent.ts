import { usePage } from '@inertiajs/react';

interface ConsentState {
    given: boolean;
    marketing: boolean;
}

function setCookie(name: string, value: string, days: number): void {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

function getCookie(name: string): string | null {
    const match = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`));
    return match ? decodeURIComponent(match[1]) : null;
}

function saveAndReload(marketing: boolean): void {
    const consent = JSON.stringify({ necessary: true, marketing });
    setCookie('cookie_consent', consent, 365);
    window.location.reload();
}

/**
 * Hook for use inside Inertia components — reads consent from shared props.
 */
export function useConsent() {
    const { consent } = usePage<{ consent: ConsentState }>().props;

    return {
        hasGivenConsent: consent.given,
        hasMarketingConsent: consent.marketing,
        acceptAll: () => saveAndReload(true),
        rejectAll: () => saveAndReload(false),
        savePreferences: ({ marketing }: { marketing: boolean }) => saveAndReload(marketing),
    };
}

/**
 * Reads consent state directly from the cookie (works outside Inertia context).
 */
export function getConsentFromCookie(): ConsentState {
    const raw = getCookie('cookie_consent');
    if (!raw) {
        return { given: false, marketing: false };
    }

    try {
        const parsed = JSON.parse(raw);
        return {
            given: true,
            marketing: Boolean(parsed.marketing),
        };
    } catch {
        return { given: false, marketing: false };
    }
}
