import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useEffect, useRef } from 'react';
import AppLink from '@/components/appLink';
import DevLabel from '@/components/devLabel';
import type { CardGridData } from '@/types/dto/sections';
import BordersDecorations from '@/components/bordersDecorations';

gsap.registerPlugin(ScrollTrigger);

export default function CardGrid(props: CardGridData) {
    const containerRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        const ctx = gsap.context(() => {
            const svgs = containerRef.current?.querySelectorAll('.card-grid-icon svg');
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
        <div ref={containerRef} className="container relative my-24">
            <DevLabel name="CardGrid" />
            <div className="text-center">
                <p className="kicker">{props.kicker}</p>
                <h2 className="section__title">{props.title}</h2>
                <p>{props.subtitle}</p>
            </div>
            <div className="relative">
                <BordersDecorations />
                <div className="relative mt-12 grid grid-cols-1 gap-px bg-mercury-200 p-px md:grid-cols-3">
                    {props.columns.map((column, index) => (
                        <div key={index} className="grid grid-cols-3 items-center gap-8 bg-white p-8 md:grid-cols-1">
                            <div className="card-grid-icon" dangerouslySetInnerHTML={{ __html: column.icon }} />
                            <div className="col-span-2 md:col-span-1">
                                <p className="mb-2 text-xl font-medium">{column.title}</p>
                                <p>{column.description}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
            <div className="mt-12 text-center">
                <AppLink href={props.link.href} title={props.link.title} className="button__primary relative">
                    <span> {props.link.title}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </AppLink>
            </div>
        </div>
    );
}
