import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import Features from '@/components/sections/features';
import Hero from '@/components/sections/hero';
import type { FeaturesData, HeroData } from '@/types/dto/sections';

export default function Welcome({ hero, features }: { hero: HeroData; features: FeaturesData }) {
    return (
        <>
            <Head title="Welcome" />

            <Navigation />

            <Hero {...hero} />
            <Features {...features} />
        </>
    );
}
