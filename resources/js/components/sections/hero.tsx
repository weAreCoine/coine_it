import AppLink from '@/components/appLink';
import GameOfLife from '@/components/gameOfLife';
import type { HeroData } from '@/types/dto/sections';

export default function Hero(props: HeroData) {
    return (
        <section id="hero" aria-labelledby="heroLabel" className="relative">
            <div className="relative container my-20 grid grid-cols-1 items-center gap-12 md:grid-cols-2">
                <div className="relative">
                    <div className="absolute top-full left-0 z-[-1] -translate-x-1/4 -translate-y-1/2 text-mercury-200">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 381 382" fill="none">
                                <rect x="0.509766" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="0.509766" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="76.5098" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="152.51" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="228.51" y="1" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="305" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="229" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="153" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="77" width="76" height="76" stroke="currentColor"></rect>
                                <rect x="304.51" y="1" width="76" height="76" stroke="currentColor"></rect>
                            </svg>
                        </div>
                        <div className="absolute top-0 left-0 h-full w-full bg-radial from-transparent to-white to-80%"></div>
                    </div>
                    <h1 id="heroLabel" className="page__title relative mb-4">
                        {props.title}
                    </h1>
                    <p className="relative mb-8">{props.description}</p>
                    <AppLink href={props.link.href} title={props.link.title} className="button__primary relative">
                        <span> {props.link.title}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </AppLink>
                </div>
                <div className="aspect-square">
                    <GameOfLife />
                </div>
            </div>
        </section>
    );
}
