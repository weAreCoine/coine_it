import { usePage } from '@inertiajs/react';

type DevLabelProps = {
    name: string;
};

export default function DevLabel({ name }: DevLabelProps) {
    const { env } = usePage().props;

    if (env !== 'local') {
        return null;
    }

    return (
        <div className="pointer-events-none absolute top-0 right-0 z-50 bg-black/80 px-1.5 py-0.5 text-xs text-white">
            {name}
        </div>
    );
}
