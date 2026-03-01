import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import About from '@/components/sections/about';
import Features from '@/components/sections/features';
import GetInTouch from '@/components/sections/getInTouch';
import Hero from '@/components/sections/hero';
import Slider from '@/components/sections/slider';
import type { AboutData, BlogData, FeaturesData, GetInTouchData, HeroData, SliderData } from '@/types/dto/sections';
import Blog from '@/components/sections/blog';

export default function Welcome({
    hero,
    features,
    about,
    getInTouch,
    slider,
    blog,
}: {
    hero: HeroData;
    features: FeaturesData;
    about: AboutData;
    getInTouch: GetInTouchData;
    slider: SliderData;
    blog: BlogData;
}) {
    return (
        <>
            <Head title="Welcome" />

            <Navigation />
            <Hero {...hero} />
            <Slider {...slider} />
            <Features {...features} />
            <GetInTouch {...getInTouch} />
            <About {...about} />
            <Blog {...blog} />
        </>
    );
}
