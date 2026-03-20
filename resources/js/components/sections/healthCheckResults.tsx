import { clsx } from 'clsx';

export type HealthCheckResultsProps = {
    score: number;
    maxScore: number;
    rangeColor: string;
    rangeLabel: string;
    rangeMessage: string;
    benchmarkText: string;
    motivationalTitle: string;
    motivationalText: string;
    findings: { color: 'r' | 'g' | 'a'; title: string; description: string }[];
    showMotivational?: boolean;
};

function FindingDot({ color }: { color: 'r' | 'a' | 'g' }) {
    return (
        <span
            className={clsx('mt-1.5 inline-block size-2.5 shrink-0 rounded-full', {
                'bg-red-500': color === 'r',
                'bg-amber-500': color === 'a',
                'bg-green-500': color === 'g',
            })}
        />
    );
}

export default function HealthCheckResults({
    score,
    rangeColor,
    rangeLabel,
    rangeMessage,
    benchmarkText,
    motivationalTitle,
    motivationalText,
    findings,
    showMotivational = true,
}: HealthCheckResultsProps) {
    return (
        <div>
            <div className="mb-12 text-center">
                <p className="font-display text-7xl font-bold" style={{ color: rangeColor }}>
                    {score}
                </p>
                <p className="text-lg text-white/40">/100</p>

                <div className="mx-auto mt-6 h-2 max-w-md overflow-hidden rounded-full bg-white/10">
                    <div
                        className="h-full rounded-full transition-all duration-700 ease-out"
                        style={{ width: `${score}%`, backgroundColor: rangeColor }}
                    />
                </div>

                <p className="mx-auto mt-3 max-w-md text-xs text-white/30">{benchmarkText}</p>

                <p className="mt-6 font-display text-xl">{rangeLabel}</p>
                <p className="mx-auto mt-2 max-w-lg text-sm text-white/60">{rangeMessage}</p>
            </div>

            <div className="mb-8">
                <h4 className="mb-4 text-sm font-medium tracking-wider text-white/40 uppercase">Aree di attenzione</h4>
                <div className="space-y-4">
                    {findings.map((finding, index) => (
                        <div key={index} className="flex gap-3 border border-white/10 p-4">
                            <FindingDot color={finding.color} />
                            <div>
                                <p className="font-medium">{finding.title}</p>
                                <p className="mt-1 text-sm text-white/60">{finding.description}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {showMotivational && (
                <div className="mb-8">
                    <h4 className="mb-2 font-display text-lg">{motivationalTitle}</h4>
                    <p className="text-sm text-white/60">{motivationalText}</p>
                </div>
            )}
        </div>
    );
}
