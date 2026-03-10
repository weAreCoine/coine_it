declare global {
    interface Window {
        gtag?: (...args: unknown[]) => void;
    }
}

interface GoogleAnalyticsProps {
    measurementId: string;
    enabled: boolean;
    flashEvents: FlashEvent[];
}

interface FlashEvent {
    eventName: string;
    params: Record<string, unknown>;
}

function trackEvent(eventName: string, params: Record<string, unknown> = {}): void {
    if (!window.gtag) {
        return;
    }

    window.gtag('event', eventName, params);
}

/**
 * Handles GA4 tracking from an Inertia navigate event.
 * Call this from router.on('navigate') in app.tsx.
 */
export function handleGANavigation(pageProps: Record<string, unknown>): void {
    const ga = pageProps.googleAnalytics as GoogleAnalyticsProps | undefined;
    const consent = pageProps.consent as { marketing?: boolean } | undefined;

    if (!consent?.marketing) {
        return;
    }

    if (!ga?.enabled) {
        return;
    }

    // gtag.js auto-tracks page_view on config, but for SPA navigations
    // we need to manually send the page_view with the updated page info
    trackEvent('page_view', {
        page_location: window.location.href,
        page_title: document.title,
    });

    // Handle flash events (e.g., generate_lead after form submission redirect)
    if (ga.flashEvents?.length) {
        for (const event of ga.flashEvents) {
            trackEvent(event.eventName, event.params);
        }
    }
}
