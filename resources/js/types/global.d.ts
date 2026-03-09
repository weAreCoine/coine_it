import type { Auth } from '@/types/auth';

interface MetaPixelSharedProps {
    eventId: string;
    pixelId: string;
    enabled: boolean;
    flashEvents: Array<{
        eventName: string;
        data: Record<string, unknown>;
        eventId: string;
    }>;
}

interface ConsentProps {
    given: boolean;
    marketing: boolean;
}

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            env: string;
            auth: Auth;
            sidebarOpen: boolean;
            consent: ConsentProps;
            metaPixel: MetaPixelSharedProps;
            [key: string]: unknown;
        };
    }
}
