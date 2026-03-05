import { useCallback, useEffect, useRef } from 'react';

const EXTRACTIONS_PER_SECOND = 120;
const LAND_COLORS = ['#e3e3e3', '#a3a3a3', '#535353', '#000000'] as const;
const LAND_COLOR_BLACK = '#000000';
const LAND_COLOR_INITIAL = '#e3e3e3';

export default function AnimatedLand({ className = '' }: { className?: string }) {
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

    return <div ref={landRef} className={`overflow-hidden ${className}`} />;
}
