import { usePage } from '@inertiajs/react';

interface ConsentState {
    given: boolean;
    marketing: boolean;
    analytics: boolean;
}

interface ConsentChoices {
    marketing: boolean;
    analytics: boolean;
}

function setCookie(name: string, value: string, days: number): void {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/; SameSite=Lax`;
}

function getCookie(name: string): string | null {
    const match = document.cookie.match(new RegExp(`(?:^|; )${name}=([^;]*)`));
    return match ? decodeURIComponent(match[1]) : null;
}

function saveConsent({ marketing, analytics }: ConsentChoices): void {
    const consent = JSON.stringify({ necessary: true, marketing, analytics });
    setCookie('cookie_consent', consent, 365);
    // Full reload (not router.reload): server-rendered tracking script tags in
    // app.blade.php are gated by CookieConsent helpers and only inject into the
    // DOM at full page render — Inertia's soft reload would refresh shared props
    // but leave the existing DOM intact.
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
        hasAnalyticsConsent: consent.analytics,
        acceptAll: () => saveConsent({ marketing: true, analytics: true }),
        rejectAll: () => saveConsent({ marketing: false, analytics: false }),
        savePreferences: (choices: ConsentChoices) => saveConsent(choices),
    };
}

/**
 * Reads consent state directly from the cookie (works outside Inertia context).
 * Cookies persisted before the `analytics` category was introduced are treated
 * as having `analytics: false` so legacy users must opt in explicitly.
 */
export function getConsentFromCookie(): ConsentState {
    const raw = getCookie('cookie_consent');
    if (!raw) {
        return { given: false, marketing: false, analytics: false };
    }

    try {
        const parsed = JSON.parse(raw);
        return {
            given: true,
            marketing: Boolean(parsed.marketing),
            analytics: Boolean(parsed.analytics),
        };
    } catch {
        return { given: false, marketing: false, analytics: false };
    }
}
