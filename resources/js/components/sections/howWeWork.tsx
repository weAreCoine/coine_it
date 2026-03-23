import DevLabel from '@/components/devLabel';
import type { WorkStep } from '@/types/dto/healthCheck';

type HowWeWorkProps = {
    steps: WorkStep[];
};

export default function HowWeWork({ steps }: HowWeWorkProps) {
    return (
        <section id="how-we-work" aria-labelledby="howWeWorkLabel" className="relative">
            <DevLabel name="HowWeWork" />
            <div className="container my-24">
                <div className="gap-x-12">
                    <div className="mb-12">
                        <p className="kicker mb-2">Come funziona</p>
                        <h2 id="howWeWorkLabel" className="section__title">
                            Dalla compilazione
                            <br />
                            alla diagnosi
                        </h2>
                    </div>
                    <div className="relative grid grid-cols-1 gap-px bg-mercury-200 p-px sm:grid-cols-2 lg:grid-cols-4">
                        {steps.map((step) => (
                            <div key={step.number} className="bg-white p-6">
                                <p className="kicker mb-6 text-4xl font-semibold">{step.number}</p>
                                <p className="mb-1 font-medium">{step.title}</p>
                                <p className="text-sm text-mercury-500">{step.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
                <h3 className="mt-8 mb-2 font-medium">Cosa succede dopo?</h3>
                <p className="max-w-3xl text-mercury-500">
                    Se scegli di prenotare un incontro, lo scheduling avviene direttamente online. Se non sei ancora pronto, riceverai contenuti
                    calibrati sulle tue risposte attraverso una sequenza email dedicata. Niente chiamate indesiderate, niente spam.
                </p>
            </div>
        </section>
    );
}
