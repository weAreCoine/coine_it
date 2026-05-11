import { useEffect } from 'react';
import { trackMetaPixelEvent } from '@/hooks/useMetaPixel';
import { generateUuid } from '@/lib/uuid';

/**
 * Selectors for the contact micro-conversions Meta calls "Contact": clicks
 * on tel: / mailto: links plus the common WhatsApp deep links. Anchored on
 * `^=` / `*=` because most of our CTAs build the href dynamically so we
 * can't enumerate them at compile time.
 */
const CONTACT_LINK_SELECTOR =
    'a[href^="tel:"], a[href^="mailto:"], a[href*="wa.me"], a[href*="whatsapp.com"]';

/**
 * Mounts a single delegated click listener on `document` that fires the
 * Meta `Contact` event whenever a visitor clicks a tel/mailto/WhatsApp link.
 *
 * Delegation keeps the cost flat regardless of how many CTAs the page renders
 * and survives Inertia client-side navigations: the listener is attached
 * once on the root layout and the Inertia DOM mutations route through it.
 *
 * Each click produces a fresh event id — micro-conversions don't have a
 * server-side mirror to deduplicate against, so reusing the page-wide id
 * would just collapse multiple clicks into one in Meta's view.
 */
export function useContactClickTracking(): void {
    useEffect(() => {
        const onClick = (event: MouseEvent) => {
            if (!hasMarketingConsent()) {
                return;
            }

            const target = event.target as Element | null;
            const anchor = target?.closest?.(CONTACT_LINK_SELECTOR) as HTMLAnchorElement | null;

            if (!anchor) {
                return;
            }

            trackMetaPixelEvent('Contact', generateUuid(), {
                content_name: contactChannelFromHref(anchor.href),
            });
        };

        document.addEventListener('click', onClick, { capture: true });

        return () => {
            document.removeEventListener('click', onClick, { capture: true });
        };
    }, []);
}

function hasMarketingConsent(): boolean {
    const raw = readCookie('cookie_consent');

    if (!raw) {
        return false;
    }

    try {
        const decoded = JSON.parse(decodeURIComponent(raw)) as { marketing?: boolean };

        return decoded.marketing === true;
    } catch {
        return false;
    }
}

function readCookie(name: string): string | null {
    const prefix = `${name}=`;
    const cookies = document.cookie ? document.cookie.split('; ') : [];

    for (const entry of cookies) {
        if (entry.startsWith(prefix)) {
            return entry.slice(prefix.length);
        }
    }

    return null;
}

function contactChannelFromHref(href: string): string {
    if (href.startsWith('tel:')) {
        return 'phone';
    }

    if (href.startsWith('mailto:')) {
        return 'email';
    }

    if (href.includes('wa.me') || href.includes('whatsapp.com')) {
        return 'whatsapp';
    }

    return 'other';
}
