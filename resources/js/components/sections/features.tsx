import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useEffect, useRef } from 'react';
import type { FeaturesData } from '@/types/dto/sections';

gsap.registerPlugin(ScrollTrigger);

export default function Features(props: FeaturesData) {
    const containerRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        const ctx = gsap.context(() => {
            const svgs = containerRef.current?.querySelectorAll('.features-icon svg');
            if (!svgs?.length) return;

            svgs.forEach((svg) => {
                const paths = Array.from(svg.querySelectorAll('path'));

                const sortedPaths = paths.sort((a, b) => {
                    const aCoords = a.getAttribute('d')?.match(/M\s*([\d.]+)[,\s]+([\d.]+)/);
                    const bCoords = b.getAttribute('d')?.match(/M\s*([\d.]+)[,\s]+([\d.]+)/);
                    const ay = parseFloat(aCoords?.[2] ?? '0');
                    const by = parseFloat(bCoords?.[2] ?? '0');
                    if (Math.abs(ay - by) > 1) return ay - by;
                    const ax = parseFloat(aCoords?.[1] ?? '0');
                    const bx = parseFloat(bCoords?.[1] ?? '0');
                    return ax - bx;
                });

                gsap.set(sortedPaths, { fill: '#e3e3e3' });

                gsap.to(sortedPaths, {
                    fill: '#000000',
                    stagger: { each: 0.02 },
                    scrollTrigger: {
                        trigger: svg,
                        start: 'top 80%',
                        end: 'bottom 20%',
                        scrub: true,
                    },
                });
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
                            <div className="features-icon" dangerouslySetInnerHTML={{ __html: column.icon }} />
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
