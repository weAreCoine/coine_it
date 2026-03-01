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
            <div>
                {props.articles.map((article: Article, index) => (
                    <article key={index}>
                        <p>{article.title}</p>
                    </article>
                ))}
            </div>
        </div>
    );
}
