import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { useCallback, useEffect, useRef } from 'react';
import type { AboutData } from '@/types/dto/sections';

gsap.registerPlugin(ScrollTrigger);

const EXTRACTIONS_PER_SECOND = 120;

const DOT_COLORS = ['#e3e3e3', '#a3a3a3', '#535353', '#000000'] as const;
const DOT_COLOR_BLACK = '#000000';
const DOT_COLOR_INITIAL = '#e3e3e3';

function parseScalar(scalar: string): { number: number; suffix: string } {
    const match = scalar.match(/^(\d+)(.*)/);
    if (!match) return { number: 0, suffix: scalar };
    return { number: parseInt(match[1], 10), suffix: match[2] };
}

export default function About(props: AboutData) {
    const sectionRef = useRef<HTMLDivElement | null>(null);
    const dotsRef = useRef<HTMLDivElement | null>(null);
    const dotsIntervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const startDotsAnimation = useCallback(() => {
        const container = dotsRef.current;
        if (!container) return;

        const paths = Array.from(container.querySelectorAll<SVGPathElement>('path'));
        if (!paths.length) return;

        paths.forEach((p) => p.setAttribute('fill', DOT_COLOR_INITIAL));

        const colorMap = new Map<SVGPathElement, string>();
        paths.forEach((p) => colorMap.set(p, DOT_COLOR_INITIAL));

        dotsIntervalRef.current = setInterval(() => {
            const nonBlack = paths.filter((p) => colorMap.get(p) !== DOT_COLOR_BLACK);
            if (!nonBlack.length) {
                if (dotsIntervalRef.current) clearInterval(dotsIntervalRef.current);
                return;
            }

            const dot = nonBlack[Math.floor(Math.random() * nonBlack.length)];
            const currentColor = colorMap.get(dot)!;
            const available = DOT_COLORS.filter((c) => c !== currentColor);
            const nextColor = available[Math.floor(Math.random() * available.length)];

            dot.setAttribute('fill', nextColor);
            colorMap.set(dot, nextColor);
        }, 1000 / EXTRACTIONS_PER_SECOND);
    }, []);

    useEffect(() => {
        const container = dotsRef.current;
        if (!container) return;

        fetch('/svg/dots.svg')
            .then((res) => res.text())
            .then((svgText) => {
                container.innerHTML = svgText;
                const svg = container.querySelector('svg');
                if (svg) {
                    svg.style.width = '100%';
                    svg.style.height = '100%';
                }

                ScrollTrigger.create({
                    trigger: container,
                    start: 'top 85%',
                    onEnter: () => startDotsAnimation(),
                });
            });

        return () => {
            if (dotsIntervalRef.current) clearInterval(dotsIntervalRef.current);
        };
    }, [startDotsAnimation]);

    useEffect(() => {
        const ctx = gsap.context(() => {
            const counters = sectionRef.current?.querySelectorAll<HTMLSpanElement>('.scalar-value');
            if (!counters?.length) return;

            counters.forEach((el) => {
                const target = parseInt(el.dataset.target ?? '0', 10);
                const proxy = { value: 0 };

                gsap.to(proxy, {
                    value: target,
                    duration: 2,
                    ease: 'power2.out',
                    scrollTrigger: {
                        trigger: el,
                        start: 'top 85%',
                        toggleActions: 'play none none none',
                    },
                    onUpdate: () => {
                        el.textContent = Math.round(proxy.value).toString();
                    },
                });
            });
        }, sectionRef);

        return () => ctx.revert();
    }, []);

    return (
        <section ref={sectionRef} className="container my-32">
            <div className="grid grid-cols-1 divide-y divide-mercury-200 border border-mercury-200 lg:grid-cols-12 lg:divide-x lg:divide-y-0">
                <div className="bg-mercury-50 lg:col-span-7">
                    <div className="flex h-full flex-col justify-between px-4 pt-16 pb-8 sm:px-10">
                        <div>
                            <p className="kicker">{props.kicker}</p>
                            <p className="mb-2 text-3xl font-semibold">{props.title}</p>
                            <p className="section__content">{props.subtitle}</p>
                        </div>
                        <div ref={dotsRef} className="mt-8" />
                    </div>
                </div>
                <div className="lg:col-span-5">
                    <div className="grid grid-cols-1 divide-y divide-mercury-200 sm:grid-cols-3 sm:divide-x sm:divide-y-0 lg:grid-cols-1 lg:grid-rows-3 lg:divide-x-0 lg:divide-y">
                        {props.skills.map((skill, key) => {
                            const { number, suffix } = parseScalar(skill.scalar);
                            return (
                                <div
                                    key={key}
                                    className="flex items-center gap-4 px-4 py-8 sm:flex-col sm:items-start sm:p-8 lg:flex-row lg:items-center"
                                >
                                    <div
                                        className="flex aspect-square items-center justify-center border p-4"
                                        dangerouslySetInnerHTML={{ __html: skill.icon }}
                                    ></div>
                                    <div>
                                        <p className="mb-1 text-2xl font-semibold sm:text-4xl">
                                            <span className="scalar-value" data-target={number}>
                                                0
                                            </span>
                                            {suffix}
                                        </p>
                                        <p className="text-sm sm:text-base">{skill.description}</p>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                    <a href={props.link.href} title={props.link.title} className="button__primary relative flex py-6 text-center">
                        <span> {props.link.title}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    );
}
