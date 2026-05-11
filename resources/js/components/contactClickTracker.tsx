import { useContactClickTracking } from '@/hooks/useContactClickTracking';

/**
 * Headless component that mounts the document-level delegated click listener
 * for the Meta `Contact` micro-conversion. Sits next to the cookie banner so
 * the layout stays Inertia-friendly: one listener per page tree, never
 * re-attached during SPA navigations.
 */
export default function ContactClickTracker(): null {
    useContactClickTracking();

    return null;
}
