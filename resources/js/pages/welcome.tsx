import { Head } from '@inertiajs/react';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import About from '@/components/sections/about';
import Blog from '@/components/sections/blog';
import Features from '@/components/sections/features';
import GetInTouch from '@/components/sections/getInTouch';
import Hero from '@/components/sections/hero';
import Services from '@/components/sections/services';
import Slider from '@/components/sections/slider';
import type { AboutData, BlogData, FeaturesData, GetInTouchData, HeroData, ServicesData, SliderData } from '@/types/dto/sections';

export default function Welcome({
    hero,
    features,
    about,
    getInTouch,
    slider,
    blog,
    services,
}: {
    hero: HeroData;
    features: FeaturesData;
    about: AboutData;
    getInTouch: GetInTouchData;
    slider: SliderData;
    blog: BlogData;
    services: ServicesData;
}) {
    return (
        <>
            <Head title="Welcome" />
            <Navigation />
            <Hero {...hero} />
            <Slider {...slider} />
            <Features {...features} />
            <GetInTouch {...getInTouch} />
            <Services {...services} />
            <About {...about} />
            <Blog {...blog} />
            <Colophon />
        </>
    );
}
