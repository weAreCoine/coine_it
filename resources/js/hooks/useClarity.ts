import Clarity from '@microsoft/clarity';

interface ClarityProps {
    projectId: string;
    enabled: boolean;
    testMode: boolean;
}

let clarityInitialized = false;
let testModeActive = false;

/**
 * Toggles the verbose console logging used to diagnose Clarity firings.
 * Driven by the `clarity.testMode` shared prop, which is true only when
 * CLARITY_ENABLED=true and CLARITY_TEST_MODE_ENABLED=true server-side.
 */
export function setClarityTestMode(enabled: boolean): void {
    testModeActive = enabled;
}

/**
 * Initializes Microsoft Clarity if analytics consent is granted, the
 * server-side `clarity.enabled` flag is true, and the visitor is NOT
 * authenticated. Logged-in users (admins) are intentionally excluded
 * from tracking to avoid polluting analytics data.
 *
 * Safe to call repeatedly: the SDK is initialized only once per page load.
 * Clarity's SDK auto-tracks SPA navigations after init() via History API.
 */
export function handleClarityNavigation(pageProps: Record<string, unknown>): void {
    if (typeof window === 'undefined') {
        return;
    }

    const clarityProps = pageProps.clarity as ClarityProps | undefined;
    const consent = pageProps.consent as { analytics?: boolean } | undefined;
    const auth = pageProps.auth as { user?: { id: number | string } | null } | undefined;

    setClarityTestMode(clarityProps?.testMode === true);

    if (auth?.user) {
        return;
    }

    if (!consent?.analytics || !clarityProps?.enabled) {
        return;
    }

    if (clarityInitialized) {
        return;
    }

    if (testModeActive) {
        console.info('[Clarity TEST] init', clarityProps.projectId);
    }

    Clarity.init(clarityProps.projectId);
    clarityInitialized = true;
}

/**
 * Fires a Clarity custom event. No-op until init() has run.
 */
export function trackClarityEvent(eventName: string): void {
    if (!clarityInitialized) {
        return;
    }

    if (testModeActive) {
        console.info('[Clarity TEST] event', eventName);
    }

    Clarity.event(eventName);
}
