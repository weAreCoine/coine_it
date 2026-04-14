import { Head } from '@inertiajs/react';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import ArticleGrid from '@/components/sections/articleGrid';
import CardGrid from '@/components/sections/cardGrid';
import ContentStats from '@/components/sections/contentStats';
import CtaBanner from '@/components/sections/ctaBanner';
import FeaturePromo from '@/components/sections/featurePromo';
import Hero from '@/components/sections/hero';
import Marquee from '@/components/sections/marquee';
import TabSection from '@/components/sections/tabSection';
import type {
    ArticleGridData,
    CardGridData,
    ContentStatsData,
    CtaBannerData,
    FeaturePromoData,
    HeroData,
    MarqueeData,
    TabSectionData,
} from '@/types/dto/sections';

export default function Welcome({
    hero,
    cardGrid,
    contentStats,
    ctaBanner,
    marquee,
    articleGrid,
    tabSection,
    featurePromo,
}: {
    hero: HeroData;
    cardGrid: CardGridData;
    contentStats: ContentStatsData;
    ctaBanner: CtaBannerData;
    marquee: MarqueeData;
    articleGrid: ArticleGridData;
    tabSection: TabSectionData;
    featurePromo: FeaturePromoData;
}) {
    return (
        <>
            <Head title="Agenzia Marketing e Sviluppo Web" />
            <Navigation />
            <Hero {...hero} />
            <Marquee {...marquee} />
            <CardGrid {...cardGrid} />
            <CtaBanner {...ctaBanner} />
            <TabSection {...tabSection} />
            <FeaturePromo {...featurePromo} />
            <ContentStats {...contentStats} />
            <ArticleGrid {...articleGrid} />
            <Colophon />
        </>
    );
}
