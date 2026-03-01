import type { BlogData } from '@/types/dto/sections';
import Article = App.Models.Article;

export default function Blog(props: BlogData) {
    return (
        <div className="container my-32">
            <div className="flex items-end justify-between">
                <div className="">
                    <p className="kicker">{props.kicker}</p>
                    <h2 className="section__title">{props.title}</h2>
                    <p>{props.subtitle}</p>
                </div>
                <div>
                    <a href={props.link.href} title={props.link.title} className="button__primary relative flex py-6 text-center">
                        <span> {props.link.title}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
            <div className="mt-8 grid grid-cols-2 gap-6 divide-x divide-mercury-200 border border-mercury-200">
                {props.articles.map((article: Article, index) => (
                    <a href="#">
                        <article key={index}>
                            <div className="p-4">
                                <div className="mb-2 bg-mercury-50 p-2">
                                    <img src={article.cover} alt={article.title} />
                                </div>
                                <p className="mb-2 text-2xl font-medium">{article.title}</p>
                                <p className="mb-2">{article.excerpt}</p>
                                <p className="text-sm uppercase">{article.created_at}</p>
                            </div>
                            <div className="flex justify-end">
                                <p className="flex aspect-square w-12 items-center justify-center border border-mercury-200 bg-mercury-50">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" className="size-4">
                                        <path
                                            fill-rule="evenodd"
                                            d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </p>
                            </div>
                        </article>
                    </a>
                ))}
            </div>
        </div>
    );
}
