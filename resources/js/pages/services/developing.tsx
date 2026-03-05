import { Head } from '@inertiajs/react';
import AnimatedLand from '@/components/animatedLand';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';
import CardGrid from '@/components/sections/cardGrid';
import type { CardGridData } from '@/types/dto/sections';

export default function Developing({ cardGrid }: { cardGrid: CardGridData }) {
    return (
        <>
            <Head title="Sviluppo Siti Web e App Mobile" />
            <Navigation />
            <div className="container mt-20 items-end justify-between md:flex">
                <div>
                    <p className="kicker mb-2">Servizi</p>
                    <h1 className="page__title">Sviluppo App e Siti Web</h1>
                </div>
                <div className="mt-2 max-w-md md:mt-0">
                    <p className="text-balance">Sviluppiamo piattaforme digitali pensate per supportare la crescita del tuo business.</p>
                </div>
            </div>
            <AnimatedLand className="container mt-12" />
            <CardGrid {...cardGrid} />
            <Colophon />
        </>
    );
}
