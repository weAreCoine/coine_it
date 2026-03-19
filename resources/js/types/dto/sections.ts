import NavigationItem = App.Entities.NavigationItem;
import BlogArticleCard = App.Entities.BlogArticleCard;

export type HeroData = {
    title: string;
    description: string;
    link: NavigationItem;
};

export type CardGridColumn = {
    icon: string;
    title: string;
    description: string;
};

export type CardGridData = {
    kicker: string;
    subtitle: string;
    title: string;
    link?: NavigationItem | null;
    columns: CardGridColumn[];
};

export type ContentStatsData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem;
    skills: {
        icon: string;
        scalar: string;
        description: string;
    }[];
};

export type CtaBannerData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem;
};

export type MarqueeData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem | null;
    slides: {
        logoUrl: string;
        title: string;
        link: NavigationItem | null;
    }[];
};

export type ArticleGridData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem;
    articles: BlogArticleCard[];
};

export type TabSectionItem = {
    tabIcon: string;
    tabLabel: string;
    icon: string;
    title: string;
    html: string;
    link: NavigationItem;
};

export type TabSectionData = {
    kicker: string;
    title: string;
    subtitle: string;
    services: TabSectionItem[];
};

export type TeamMember = { name: string; role: string; bio: string; image: string; tags: string[] };
