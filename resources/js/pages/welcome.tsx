import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import Hero from '@/components/sections/hero';
import type { HeroData } from '@/types/dto/heroSection';

export default function Welcome({ hero }: { hero: HeroData }) {
    return (
        <>
            <Head title="Welcome" />

            <Navigation />

            <Hero {...hero} />
        </>
    );
}
