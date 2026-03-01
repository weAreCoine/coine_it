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
declare namespace App.Models {
export type Article = {
excerpt: string;
timestamps: any;
incrementing: boolean;
preventsLazyLoading: boolean;
exists: boolean;
wasRecentlyCreated: boolean;
usesUniqueIds: boolean;
};
}
