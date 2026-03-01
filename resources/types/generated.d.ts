declare namespace App.Entities {
export type BlogArticleCard = {
title: string;
slug: string;
excerpt: string;
cover: string | null;
categories: string;
createdAt: string;
createdAtIso: string;
authorName: string;
};
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
id: number;
title: string;
content: string;
slug: string;
excerpt: string;
cover?: string | null;
user_id: number;
seo_title?: string | null;
seo_description?: string | null;
seo_image?: string | null;
is_published: boolean;
is_featured: boolean;
created_at: string;
updated_at: string;
deleted_at?: string | null;
};
}
