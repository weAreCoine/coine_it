import { Head } from '@inertiajs/react';
import { useCallback, useEffect, useRef } from 'react';
import BordersDecorations from '@/components/bordersDecorations';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';

const EXTRACTIONS_PER_SECOND = 120;
const LAND_COLORS = ['#e3e3e3', '#a3a3a3', '#535353', '#000000'] as const;
const LAND_COLOR_BLACK = '#000000';
const LAND_COLOR_INITIAL = '#e3e3e3';

interface NumberItem {
    scalar: string;
    description: string;
}

interface PrincipleItem {
    image: string;
    title: string;
}

interface AboutProps {
    numbers: NumberItem[];
    principles: PrincipleItem[];
}

export default function About({ numbers, principles }: AboutProps) {
    const landRef = useRef<HTMLDivElement | null>(null);
    const landIntervalRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const startLandAnimation = useCallback(() => {
        const container = landRef.current;
        if (!container) return;

        const paths = Array.from(container.querySelectorAll<SVGPathElement>('path'));
        if (!paths.length) return;

        paths.forEach((p) => p.setAttribute('fill', LAND_COLOR_INITIAL));

        const colorMap = new Map<SVGPathElement, string>();
        paths.forEach((p) => colorMap.set(p, LAND_COLOR_INITIAL));

        landIntervalRef.current = setInterval(() => {
            const nonBlack = paths.filter((p) => colorMap.get(p) !== LAND_COLOR_BLACK);
            if (!nonBlack.length) {
                if (landIntervalRef.current) clearInterval(landIntervalRef.current);
                return;
            }

            const dot = nonBlack[Math.floor(Math.random() * nonBlack.length)];
            const currentColor = colorMap.get(dot)!;
            const available = LAND_COLORS.filter((c) => c !== currentColor);
            const nextColor = available[Math.floor(Math.random() * available.length)];

            dot.setAttribute('fill', nextColor);
            colorMap.set(dot, nextColor);
        }, 1000 / EXTRACTIONS_PER_SECOND);
    }, []);

    useEffect(() => {
        const container = landRef.current;
        if (!container) return;

        fetch('/svg/land.svg')
            .then((res) => res.text())
            .then((svgText) => {
                container.innerHTML = svgText;
                const svg = container.querySelector('svg');
                if (svg) {
                    svg.style.width = 'auto';
                    svg.style.height = '100%';
                }
                startLandAnimation();
            });

        return () => {
            if (landIntervalRef.current) clearInterval(landIntervalRef.current);
        };
    }, [startLandAnimation]);

    return (
        <>
            <Head title="Chi siamo" />
            <Navigation />
            <div className="container mt-20 items-end justify-between md:flex">
                <div>
                    <p className="kicker mb-2">Chi siamo</p>
                    <h1 className="page__title">We Are Coiné</h1>
                </div>
                <div className="mt-2 max-w-md md:mt-0">
                    <p className="text-balance">
                        Siamo un team di professionisti freelance specializzati in eCommerce, performance marketing e tecnologie digitali.
                    </p>
                </div>
            </div>
            <div className="container mt-12">
                <div ref={landRef} className="overflow-hidden" />
            </div>
            <div className="container mt-8 grid grid-cols-2 items-center gap-6 md:flex md:justify-between">
                {numbers &&
                    numbers.map((number, index) => (
                        <div key={index}>
                            <div className="flex items-center justify-start gap-2">
                                <p className="text-4xl font-semibold xs:text-5xl">{number.scalar}</p>
                                <div className="inline-flex items-center justify-center rounded-full bg-mercury-100 p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 15 14" fill="none" className="size-2 xs:size-3">
                                        <path d="M7.40431 13.0625L7.4043 2" stroke="currentColor" stroke-width="1.5"></path>
                                        <path d="M13.8141 8.40703L7.40703 2L1 8.40703" stroke="currentColor" stroke-width="1.5"></path>
                                    </svg>
                                </div>
                            </div>
                            <p className="text-sm xs:text-base">{number.description}</p>
                        </div>
                    ))}
            </div>
            <div className="my-24 bg-mercury-50 py-24">
                <div className="container">
                    <div className="px-4">
                        <p className="kicker">Valori</p>
                        <h2 className="section__title">I valori che guidano Coiné</h2>
                        <p>Crediamo che la collaborazione sana porti valore a tutte le persone coinvolte.</p>
                    </div>

                    <div className="relative mt-12 grid grid-cols-1 gap-px bg-mercury-200 p-px sm:grid-cols-2 lg:grid-cols-4">
                        <BordersDecorations bg="bg-mercury-50" />

                        {principles.map((principle, index) => (
                            <div
                                key={index}
                                className="flex flex-col items-start justify-start gap-x-6 gap-y-4 bg-mercury-50 p-8 min-[300px]:flex-row min-[300px]:items-center"
                            >
                                <div className="aspect-square shrink-0 border border-black">
                                    <img src={principle.image} alt={principle.title} className="h-10 w-auto object-cover" />
                                </div>
                                <p className="text-lg font-medium min-[300px]:text-xl">{principle.title}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
            <div className="container my-24">
                <div className="text-center">
                    <p className="kicker">Il nostro team</p>
                    <h2 className="section__title">Siamo Coiné: ci presentiamo</h2>
                    <p className="mx-auto max-w-lg text-balance">
                        Siamo un team dinamico che si costruisce su misura in base ai progetti che ci vengono affidati.
                    </p>
                </div>
                <div className="relative mt-8 grid gap-px bg-mercury-200 p-px lg:grid-cols-2">
                    <BordersDecorations />
                    <div className="flex items-center justify-start gap-8 bg-white p-4">
                        <div className="aspect-square max-w-40 shrink-0 lg:aspect-9/12 xl:aspect-square">
                            <img
                                src="https://cdn.prod.website-files.com/68a342b7066c56fa60eb3b39/68a6e5ff7b59be67bb3ac207_john-carter-avatar-quantum-webflow-template.jpg"
                                alt=""
                                className="h-full w-auto object-cover"
                            />
                        </div>
                        <div className="grow">
                            <div className="flex items-baseline gap-2">
                                <p className="text-xl font-semibold">Luca Barbi</p>
                                <p className="kicker text-xs">Sviluppo e Marketing</p>
                            </div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                    <div className="flex items-center justify-start gap-8 bg-white p-4">
                        <div className="aspect-square max-w-40 shrink-0 lg:aspect-9/12 xl:aspect-square">
                            <img
                                src="https://cdn.prod.website-files.com/68a342b7066c56fa60eb3b39/68a6e5d29b2f76acd088f052_sophie-moore-avatar-quantum-webflow-template.jpg"
                                alt=""
                                className="h-full w-auto object-cover"
                            />
                        </div>
                        <div className="grow">
                            <div className="flex items-baseline gap-2">
                                <p className="text-xl font-semibold">Silvia Pallai</p>
                                <p className="kicker text-xs">Content e Marketing</p>
                            </div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        </div>
                    </div>
                </div>
            </div>
            <Colophon />
        </>
    );
}
