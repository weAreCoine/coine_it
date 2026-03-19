import DevLabel from '@/components/devLabel';
import type { WorkStep } from '@/types/dto/healthCheck';
import Faq = App.Entities.Faq;

type HowWeWorkProps = {
    steps: WorkStep[];
    faqs: Faq[];
};

export default function HowWeWork({ steps, faqs }: HowWeWorkProps) {
    return (
        <section id="how-we-work" aria-labelledby="howWeWorkLabel" className="relative">
            <DevLabel name="HowWeWork" />
            <div className="container my-24">
                <div className="grid grid-cols-2 items-start gap-x-12">
                    <div className="mb-12">
                        <p className="kicker mb-2">Metodo</p>
                        <h2 id="howWeWorkLabel" className="section__title">
                            Come lavoriamo
                        </h2>
                        <p className="max-w-2xl text-balance text-mercury-500">
                            Un sistema integrato in cui advertising, sviluppo e contenuti condividono gli stessi obiettivi, gli stessi dati e lo
                            stesso calendario.
                        </p>
                    </div>
                    <div className="relative grid grid-cols-1 gap-px bg-mercury-200 sm:grid-cols-2">
                        {steps.map((step) => (
                            <div key={step.number} className="bg-white p-6">
                                <p className="kicker mb-2 text-xs">{step.number}</p>
                                <p className="mb-1 font-medium">{step.title}</p>
                                <p className="text-sm text-mercury-500">{step.description}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
}
