import AppLink from '@/components/appLink';
import DevLabel from '@/components/devLabel';

export default function HealthCheckCta() {
    return (
        <section id="health-check-cta" className="relative overflow-hidden bg-mercury-50 py-24">
            <DevLabel name="HealthCheckCta" />
            <img
                src="/svg/mountains.svg"
                alt=""
                aria-hidden="true"
                className="object-contains pointer-events-none absolute bottom-0 left-0 h-[90%] w-full opacity-5"
            />
            <div className="relative container text-center">
                <p className="kicker mb-2">Inizia da qui</p>
                <h2 className="section__title">Parliamo del tuo e-commerce</h2>
                <p className="mx-auto mb-8 max-w-lg text-balance text-mercury-500">
                    Il punto di partenza è sempre una conversazione. Inizia con l'Health Check gratuito, oppure scrivici direttamente.
                </p>
                <div className="flex flex-wrap items-center justify-center gap-4">
                    <a href="#health-check-quiz" className="button__primary">
                        <span>Inizia l'Health Check</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                    <AppLink href="mailto:info@coine.it" className="text-sm font-medium text-mercury-500 transition-colors hover:text-white">
                        Scrivici direttamente
                    </AppLink>
                </div>
            </div>
        </section>
    );
}
