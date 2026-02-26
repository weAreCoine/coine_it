import NavigationItem = App.Entities.NavigationItem;

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
