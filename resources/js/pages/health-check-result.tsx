import { Head } from '@inertiajs/react';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import HealthCheckResults, { type HealthCheckResultsProps } from '@/components/sections/healthCheckResults';

interface HealthCheckResultProps {
    result: HealthCheckResultsProps;
    leadName: string;
}

export default function HealthCheckResult({ result, leadName }: HealthCheckResultProps) {
    return (
        <>
            <Head title="I tuoi risultati - E-commerce Health Check">
                <meta name="robots" content="noindex, nofollow" />
            </Head>
            <Navigation />
            <section className="relative bg-black py-24 text-white">
                <div className="container">
                    <div className="mb-12 text-center">
                        <p className="kicker mb-2 text-white/40">I tuoi risultati</p>
                        <h1 className="section__title text-white">{leadName ? `${leadName}, ecco la tua diagnosi` : 'Ecco la tua diagnosi'}</h1>
                    </div>
                    <div className="mx-auto max-w-2xl">
                        <HealthCheckResults {...result} />
                    </div>
                </div>
            </section>
            <Colophon marginTop={false} borderTop={true} />
        </>
    );
}
