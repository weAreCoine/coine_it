import { gsap } from 'gsap';
import { useEffect, useRef } from 'react';
import type { SliderData } from '@/types/dto/sections';

export default function Slider(props: SliderData) {
    const trackRef = useRef<HTMLDivElement | null>(null);
    const tweenRef = useRef<gsap.core.Tween | null>(null);

    useEffect(() => {
        const track = trackRef.current;
        if (!track || props.slides.length === 0) return;

        const ctx = gsap.context(() => {
            const slideWidth = track.scrollWidth / 2;

            tweenRef.current = gsap.to(track, {
                x: -slideWidth,
                duration: props.slides.length * 6,
                ease: 'none',
                repeat: -1,
            });
        }, track);

        return () => {
            tweenRef.current = null;
            ctx.revert();
        };
    }, [props.slides]);

    const handleMouseEnter = () => tweenRef.current?.pause();
    const handleMouseLeave = () => tweenRef.current?.resume();

    return (
        <div className="my-32 bg-mercury-50 py-10">
            <div className="container grid grid-cols-1 items-end gap-2 md:grid-cols-2 md:gap-32 lg:gap-64">
                <div>
                    <p className="kicker">{props.kicker}</p>
                    <h2 className="section__title text-balance">{props.title}</h2>
                </div>
                <div>
                    <p className="text-balance">{props.subtitle}</p>
                </div>
            </div>
            <div className="mt-10 overflow-hidden border-y border-mercury-200" onMouseEnter={handleMouseEnter} onMouseLeave={handleMouseLeave}>
                <div ref={trackRef} className="flex w-max">
                    {[...props.slides, ...props.slides].map((slide, index) => (
                        <a
                            key={index}
                            href={slide.link.href}
                            title={slide.link.title}
                            className="group relative flex h-24 w-48 shrink-0 items-center justify-center overflow-hidden border-r border-mercury-200 bg-mercury-50 px-8"
                        >
                            <span className="absolute inset-0 -translate-x-full bg-black transition-transform duration-300 ease-out group-hover:translate-x-0" />
                            <img
                                src={slide.logoUrl}
                                alt={slide.title}
                                className="relative h-auto max-h-12 w-full object-contain mix-blend-darken grayscale group-hover:mix-blend-lighten group-hover:invert"
                            />
                        </a>
                    ))}
                </div>
            </div>
            <div className="container mt-12 text-center">
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
