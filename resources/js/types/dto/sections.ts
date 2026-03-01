import NavigationItem = App.Entities.NavigationItem;
import BlogArticleCard = App.Entities.BlogArticleCard;

export type HeroData = {
    title: string;
    description: string;
    link: NavigationItem;
};

export type FeaturesColumn = {
    icon: string;
    title: string;
    description: string;
};

export type FeaturesData = {
    kicker: string;
    subtitle: string;
    title: string;
    link: NavigationItem;
    columns: FeaturesColumn[];
};

export type AboutData = {
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

export type GetInTouchData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem;
};

export type SliderData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem;
    slides: {
        logoUrl: string;
        title: string;
        link: NavigationItem;
    }[];
};

export type BlogData = {
    kicker: string;
    title: string;
    subtitle: string;
    link: NavigationItem;
    articles: BlogArticleCard[];
};
