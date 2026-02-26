import type { HeroData } from '@/types/dto/heroSection';
import GameOfLife from '@/components/gameOfLife';

export default function Hero(props: HeroData) {
    return (
        <div className="container my-20 grid grid-cols-1 items-center gap-x-12 md:grid-cols-2">
            <div>
                <h1 className="page__title mb-4">{props.title}</h1>
                <p className="mb-8">{props.description}</p>
                <a href={props.link.href} title={props.link.title} className="button__primary">
                    <span> {props.link.title}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
            <div className="aspect-square">
                <GameOfLife />
            </div>
        </div>
    );
}
