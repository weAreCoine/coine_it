import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import About from '@/components/sections/about';
import Features from '@/components/sections/features';
import GetInTouch from '@/components/sections/getInTouch';
import Hero from '@/components/sections/hero';
import type { AboutData, FeaturesData, GetInTouchData, HeroData } from '@/types/dto/sections';

export default function Welcome({
    hero,
    features,
    about,
    getInTouch,
}: {
    hero: HeroData;
    features: FeaturesData;
    about: AboutData;
    getInTouch: GetInTouchData;
}) {
    return (
        <>
            <Head title="Welcome" />

            <Navigation />
            <Hero {...hero} />
            <Features {...features} />
            <About {...about} />
            <GetInTouch {...getInTouch} />
        </>
    );
}
