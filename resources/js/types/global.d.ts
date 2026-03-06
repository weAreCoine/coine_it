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

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            env: string;
            auth: Auth;
            sidebarOpen: boolean;
            metaPixel: MetaPixelSharedProps;
            [key: string]: unknown;
        };
    }
}
