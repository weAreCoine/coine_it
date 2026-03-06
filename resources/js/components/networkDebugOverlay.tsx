import { useEffect, useRef, useState } from 'react';

/**
 * Temporary diagnostic overlay: shows real-time network resource loads.
 * Displays a small floating panel on mobile so you can see which requests
 * fire when the Brave loading bar appears.
 *
 * REMOVE THIS COMPONENT after debugging.
 */
export default function NetworkDebugOverlay() {
    const [entries, setEntries] = useState<string[]>([]);
    const [scrollState, setScrollState] = useState<'idle' | 'scrolling'>('idle');
    const scrollTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        const observer = new PerformanceObserver((list) => {
            const newEntries: string[] = [];
            for (const entry of list.getEntries()) {
                if (entry.entryType === 'resource') {
                    const res = entry as PerformanceResourceTiming;
                    const url = new URL(res.name, window.location.origin);
                    const short = url.hostname === window.location.hostname
                        ? url.pathname.slice(0, 60)
                        : url.hostname + url.pathname.slice(0, 40);
                    const size = res.transferSize;
                    const time = Math.round(res.duration);
                    newEntries.push(`[${time}ms ${size}B] ${short}`);
                }
            }
            if (newEntries.length > 0) {
                setEntries((prev) => [...newEntries, ...prev].slice(0, 20));
            }
        });

        observer.observe({ entryTypes: ['resource'] });

        const onScroll = () => {
            setScrollState('scrolling');
            if (scrollTimer.current) clearTimeout(scrollTimer.current);
            scrollTimer.current = setTimeout(() => {
                setScrollState('idle');
                setEntries((prev) => ['--- SCROLL STOP ---', ...prev].slice(0, 20));
            }, 150);
        };

        window.addEventListener('scroll', onScroll, { passive: true });

        return () => {
            observer.disconnect();
            window.removeEventListener('scroll', onScroll);
            if (scrollTimer.current) clearTimeout(scrollTimer.current);
        };
    }, []);

    return (
        <div
            style={{
                position: 'fixed',
                bottom: 0,
                left: 0,
                right: 0,
                maxHeight: '35vh',
                overflowY: 'auto',
                background: 'rgba(0,0,0,0.9)',
                color: '#0f0',
                fontSize: '11px',
                fontFamily: 'monospace',
                padding: '8px',
                zIndex: 99999,
                pointerEvents: 'auto',
            }}
        >
            <div style={{ color: scrollState === 'scrolling' ? '#ff0' : '#0f0', marginBottom: 4 }}>
                State: {scrollState} | Entries: {entries.length}
            </div>
            {entries.map((entry, i) => (
                <div key={i} style={{ color: entry.includes('SCROLL STOP') ? '#ff0' : '#0f0', borderBottom: '1px solid #333', padding: '2px 0' }}>
                    {entry}
                </div>
            ))}
        </div>
    );
}
