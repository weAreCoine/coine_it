declare global {
    interface Window {
        fbq?: (...args: unknown[]) => void;
    }
}

interface MetaPixelProps {
    eventId: string;
    pixelId: string;
    enabled: boolean;
    flashEvents: FlashEvent[];
}

interface FlashEvent {
    eventName: string;
    data: Record<string, unknown>;
    eventId: string;
}

function trackEvent(eventName: string, data: Record<string, unknown> = {}, eventId?: string): void {
    if (!window.fbq) {
        return;
    }

    const options: Record<string, unknown> = {};
    if (eventId) {
        options.eventID = eventId;
    }

    window.fbq('track', eventName, data, options);
}

/**
 * Handles Meta Pixel tracking from an Inertia navigate event.
 * Call this from router.on('navigate') in app.tsx.
 */
export function handleMetaPixelNavigation(pageProps: Record<string, unknown>): void {
    const metaPixel = pageProps.metaPixel as MetaPixelProps | undefined;

    const consent = pageProps.consent as { marketing?: boolean } | undefined;
    if (!consent?.marketing) {
        return;
    }

    if (!metaPixel?.enabled) {
        return;
    }

    // Track PageView with deduplication eventId
    trackEvent('PageView', {}, metaPixel.eventId);

    // Handle flash events (e.g., Lead after form submission redirect)
    if (metaPixel.flashEvents?.length) {
        for (const event of metaPixel.flashEvents) {
            trackEvent(event.eventName, event.data, event.eventId);
        }
    }
}
