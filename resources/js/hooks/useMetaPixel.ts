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

/**
 * Reads the Iubenda consent cookie and checks if purpose 3 (experience) is accepted.
 */
function hasIubendaConsent(): boolean {
    const cookies = document.cookie.split(';');

    for (const cookie of cookies) {
        const trimmed = cookie.trim();

        if (trimmed.startsWith('_iub_cs-s=') || trimmed.startsWith('_iub_cs=')) {
            try {
                const value = decodeURIComponent(trimmed.split('=').slice(1).join('='));
                const data = JSON.parse(value);

                if (data?.purposes && typeof data.purposes === 'object') {
                    return data.purposes[3] === true;
                }
            } catch {
                // Invalid cookie format
            }
        }
    }

    return false;
}

function trackEvent(eventName: string, data: Record<string, unknown> = {}, eventId?: string): void {
    if (!window.fbq || !hasIubendaConsent()) {
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
