import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';

type CategoryPageProps = {
    name: string;
    slug: string;
};

export default function Category({ name }: CategoryPageProps) {
    return (
        <>
            <Head title={name} />

            <Navigation />

            <div className="container my-16">
                <h1 className="section__title">{name}</h1>
            </div>
        </>
    );
}
