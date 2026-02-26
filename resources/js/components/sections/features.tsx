import { useEffect, useRef } from 'react';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import type { FeaturesData } from '@/types/dto/sections';

gsap.registerPlugin(ScrollTrigger);

export default function Features(props: FeaturesData) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const ctx = gsap.context(() => {
            const timeline = gsap.timeline({
                scrollTrigger: {
                    trigger: containerRef.current,
                    start: 'top 80%',
                    end: 'bottom 20%',
                    scrub: true,
                    markers: true,
                },
            });
        }, containerRef);

        return () => ctx.revert();
    }, []);

    return (
        <div ref={containerRef} className="container my-24">
            <div className="text-center">
                <p className="mb-2 text-sm font-medium text-mercury-400 uppercase">{props.kicker}</p>
                <h2 className="mb-2 text-4xl font-medium">{props.title}</h2>
                <p>{props.subtitle}</p>
            </div>
            <div className="relative">
                <div className="absolute right-full bottom-full h-32 w-32 translate-x-px translate-y-px bg-linear-to-br from-transparent to-mercury-200">
                    <div className="absolute right-px bottom-px h-full w-full bg-white"></div>
                </div>
                <div className="relative mt-12 grid grid-cols-3 divide-x divide-mercury-200 border border-mercury-200">
                    {props.columns.map((column, index) => (
                        <div key={index} className="p-8">
                            <div dangerouslySetInnerHTML={{ __html: column.icon }} />
                            <p className="mb-2 text-xl font-medium">{column.title}</p>
                            <p>{column.description}</p>
                        </div>
                    ))}
                </div>
            </div>
            <div className="mt-12 text-center">
                <a href={props.link.href} title={props.link.title} className="button__primary relative">
                    <span> {props.link.title}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
        </div>
    );
}
