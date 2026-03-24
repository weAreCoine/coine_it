declare global {
    interface Window {
        lintrk?: (action: string, data: Record<string, unknown>) => void;
    }
}

interface LinkedInProps {
    partnerId: string;
    enabled: boolean;
    flashEvents: FlashEvent[];
}

interface FlashEvent {
    conversionType: string;
}

function trackEvent(conversionId: string): void {
    if (!window.lintrk) {
        return;
    }

    window.lintrk('track', { conversion_id: conversionId });
}

/**
 * Handles LinkedIn tracking from an Inertia navigate event.
 * Call this from router.on('navigate') in app.tsx.
 */
export function handleLinkedInNavigation(pageProps: Record<string, unknown>): void {
    const linkedIn = pageProps.linkedIn as LinkedInProps | undefined;
    const consent = pageProps.consent as { marketing?: boolean } | undefined;

    if (!consent?.marketing) {
        return;
    }

    if (!linkedIn?.enabled) {
        return;
    }

    // Handle flash events (e.g., Lead after form submission redirect)
    if (linkedIn.flashEvents?.length) {
        for (const event of linkedIn.flashEvents) {
            trackEvent(event.conversionType);
        }
    }
}
