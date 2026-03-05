import BordersDecorations from '@/components/bordersDecorations';

interface PrincipleItem {
    image: string;
    title: string;
}

interface PrinciplesGridProps {
    principles: PrincipleItem[];
}

export default function PrinciplesGrid({ principles }: PrinciplesGridProps) {
    return (
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
    );
}
