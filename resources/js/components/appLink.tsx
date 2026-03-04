import { Link } from '@inertiajs/react';
import type { ComponentProps } from 'react';

type AppLinkProps = ComponentProps<'a'> & {
    external?: boolean;
    prefetch?: ComponentProps<typeof Link>['prefetch'];
};

function isExternalHref(href: string): boolean {
    return href.startsWith('mailto:') || href.startsWith('tel:') || /^https?:\/\//.test(href);
}

export default function AppLink({ external, href, prefetch = 'click', ...props }: AppLinkProps) {
    if (!href || external || isExternalHref(href)) {
        return <a href={href} {...props} />;
    }

    return <Link href={href} prefetch={prefetch} {...props} />;
}
