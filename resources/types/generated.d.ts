declare namespace App.Entities {
    export type NavigationItem = {
        href: string;
        isCurrent: boolean;
        title: string;
        routeName: string | null;
        isExternal: boolean;
        targetBlank: boolean;
        isPlaceholder: boolean;
        subItems: Array<App.Entities.NavigationItem>;
        isCallToAction: boolean;
    };
}
