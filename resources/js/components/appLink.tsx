import { type InertiaLinkProps, Link } from '@inertiajs/react';
import type { ComponentProps } from 'react';

type InertiaOnlyProps =
    | 'data'
    | 'only'
    | 'except'
    | 'headers'
    | 'method'
    | 'preserveScroll'
    | 'preserveState'
    | 'replace'
    | 'async'
    | 'queryStringArrayFormat'
    | 'onBefore'
    | 'onStart'
    | 'onProgress'
    | 'onFinish'
    | 'onCancel'
    | 'onSuccess'
    | 'onError';

type AppLinkProps = InertiaLinkProps & {
    external?: boolean;
};

function isExternalHref(href: string): boolean {
    if (href.startsWith('mailto:') || href.startsWith('tel:')) return true;
    if (/^https?:\/\//.test(href)) {
        try {
            return new URL(href).origin !== window.location.origin;
        } catch {
            return true;
        }
    }
    return false;
}

export default function AppLink({ external, href, prefetch = 'click', ...props }: AppLinkProps) {
    const hrefString = typeof href === 'string' ? href : href?.url;

    if (!hrefString || external || isExternalHref(hrefString)) {
        const anchorProps = props as Omit<typeof props, InertiaOnlyProps> as ComponentProps<'a'>;
        return <a href={hrefString} {...anchorProps} />;
    }

    return <Link href={href} prefetch={prefetch} {...props} />;
}
