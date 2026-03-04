import { gsap } from 'gsap';
import { useEffect, useRef } from 'react';
import AppLink from '@/components/appLink';
import type { CtaBannerData } from '@/types/dto/sections';

const SWEEP_DURATION = 2;
const JITTER = 0.01;
const MAX_Y = 469.46;
const TOTAL_ROWS = 32;
const ROW_TIME = SWEEP_DURATION / TOTAL_ROWS;
const FLASH_OFF = 0.08;
const BASE_FILL = 'rgb(40,40,40)';
const LINGER_CHANCE = 0.12;

// Per-row flash probability: index 0 = leading row, index 1 = trailing, etc.
const BAND_CHANCES = [0.5, 0.2];

export default function CtaBanner(props: CtaBannerData) {
    const mountainsRef = useRef<HTMLDivElement | null>(null);

    useEffect(() => {
        const container = mountainsRef.current;
        if (!container) return;

        let ctx: gsap.Context | undefined;

        fetch('/svg/mountains.svg')
            .then((res) => res.text())
            .then((svgText) => {
                if (!mountainsRef.current) return;

                container.innerHTML = svgText;

                const svg = container.querySelector('svg');
                if (svg) {
                    svg.removeAttribute('width');
                    svg.removeAttribute('height');
                    svg.removeAttribute('preserveAspectRatio');
                    svg.removeAttribute('style');
                    svg.style.height = '100%';
                    svg.style.width = 'auto';
                    svg.style.display = 'block';
                    svg.style.marginLeft = 'auto';
                }

                ctx = gsap.context(() => {
                    const paths = Array.from(container.querySelectorAll('path'));
                    if (!paths.length) return;

                    const yPositions = paths.map((path) => {
                        const match = path.getAttribute('d')?.match(/M\s*([\d.]+)[,\s]+([\d.]+)/);
                        return parseFloat(match?.[2] ?? '0');
                    });

                    // Assign each pixel to a band row (leading=0, trailing=1, …) or -1 (skip)
                    const bandRow = paths.map(() => {
                        for (let r = 0; r < BAND_CHANCES.length; r++) {
                            if (Math.random() < BAND_CHANCES[r]) return r;
                        }
                        return -1;
                    });

                    // Peak brightness: leading rows brighter, trailing rows dimmer
                    const peakFills = paths.map((_, i) => {
                        if (bandRow[i] === -1) return BASE_FILL;
                        const minB = bandRow[i] === 0 ? 140 : 80;
                        const range = bandRow[i] === 0 ? 115 : 100;
                        const b = minB + Math.floor(Math.random() * range);
                        return `rgb(${b},${b},${b})`;
                    });

                    // Linger set for up sweep
                    const lingerSet = new Set<number>();
                    paths.forEach((_, i) => {
                        if (Math.random() < LINGER_CHANCE) lingerSet.add(i);
                    });

                    const upReturnFills = paths.map((_, i) => {
                        if (lingerSet.has(i)) {
                            const b = 55 + Math.floor(Math.random() * 50);
                            return `rgb(${b},${b},${b})`;
                        }
                        return BASE_FILL;
                    });

                    // Stagger offsets include band row delay (trailing rows flash later)
                    const bandDelay = (i: number) => (bandRow[i] === -1 ? 0 : bandRow[i] * ROW_TIME);
                    const downOffsets = yPositions.map((y, i) => (y / MAX_Y) * SWEEP_DURATION + Math.random() * JITTER + bandDelay(i));
                    const upOffsets = yPositions.map((y, i) => ((MAX_Y - y) / MAX_Y) * SWEEP_DURATION + Math.random() * JITTER + bandDelay(i));

                    // Timeline order: up → pause → down → pause
                    // This way linger pixels survive until the down sweep clears them.
                    // On repeat, state is all BASE_FILL so the reset is invisible.
                    const tl = gsap.timeline({ repeat: -1 });

                    // --- Up sweep (with linger) ---
                    const upStart = 0;

                    tl.to(
                        paths,
                        {
                            fill: (index: number) => peakFills[index],
                            duration: ROW_TIME,
                            stagger: (index: number) => upOffsets[index],
                        },
                        upStart,
                    );

                    tl.to(
                        paths,
                        {
                            fill: (index: number) => upReturnFills[index],
                            duration: FLASH_OFF,
                            stagger: (index: number) => upOffsets[index] + ROW_TIME,
                        },
                        upStart,
                    );

                    // Pause 3s (linger pixels stay lit)
                    const upEnd = Math.max(...upOffsets) + ROW_TIME + FLASH_OFF;
                    tl.set({}, {}, upEnd + 3);

                    // --- Down sweep (clears linger as it passes) ---
                    const downStart = upEnd + 3;

                    tl.to(
                        paths,
                        {
                            fill: (index: number) => peakFills[index],
                            duration: ROW_TIME,
                            stagger: (index: number) => downOffsets[index],
                        },
                        downStart,
                    );

                    tl.to(
                        paths,
                        {
                            fill: BASE_FILL,
                            duration: FLASH_OFF,
                            stagger: (index: number) => downOffsets[index] + ROW_TIME,
                        },
                        downStart,
                    );

                    // Pause 3s
                    const downEnd = Math.max(...downOffsets) + ROW_TIME + FLASH_OFF;
                    tl.set({}, {}, downStart + downEnd + 3);
                }, container);
            });

        return () => {
            ctx?.revert();
        };
    }, []);

    return (
        <div className="my-32 bg-black pt-10 text-white">
            <div className="relative">
                <div ref={mountainsRef} className="absolute inset-x-0 top-10 bottom-0 overflow-hidden text-mercury-400" />
                <div className="relative container pt-24 pb-48">
                    <div className="max-w-lg">
                        <p className="kicker">{props.kicker}</p>
                        <h2 className="section__title">{props.title}</h2>
                        <p className="text-balance">{props.subtitle}</p>
                        <div className="mt-8">
                            <AppLink href={props.link.href} title={props.link.title} className="button__primary relative bg-white text-black">
                                <span> {props.link.title}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </AppLink>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
