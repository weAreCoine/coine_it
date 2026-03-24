import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createRoot } from 'react-dom/client';
import CookieConsentBanner from '@/components/cookieConsentBanner';
import { handleGANavigation } from '@/hooks/useGoogleAnalytics';
import { handleLinkedInNavigation } from '@/hooks/useLinkedIn';
import { handleMetaPixelNavigation } from '@/hooks/useMetaPixel';
import '../css/app.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

router.on('navigate', (event) => {
    handleMetaPixelNavigation(event.detail.page.props);
    handleGANavigation(event.detail.page.props);
    handleLinkedInNavigation(event.detail.page.props);
});

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => resolvePageComponent(`./pages/${name}.tsx`, import.meta.glob('./pages/**/*.tsx')),
    setup({ el, App, props }) {
        const root = createRoot(el);
        root.render(
            <>
                <App {...props} />
                <CookieConsentBanner />
            </>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});
