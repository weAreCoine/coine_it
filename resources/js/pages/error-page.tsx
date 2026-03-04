import { Head } from '@inertiajs/react';
import AppLink from '@/components/appLink';
import Colophon from '@/components/colophon';
import Navigation from '@/components/navigation';

const errorMessages: Record<number, { title: string; description: string }> = {
    403: {
        title: 'Accesso negato',
        description: 'Non hai i permessi necessari per accedere a questa pagina.',
    },
    404: {
        title: 'Pagina non trovata',
        description: 'La pagina che stai cercando non esiste o potrebbe essere stata spostata.',
    },
    500: {
        title: 'Errore del server',
        description: 'Si è verificato un problema interno. Riprova più tardi.',
    },
    503: {
        title: 'Servizio non disponibile',
        description: 'Il sito è temporaneamente in manutenzione. Riprova tra qualche minuto.',
    },
};

const fallback = {
    title: 'Errore imprevisto',
    description: 'Si è verificato un errore. Riprova più tardi.',
};

export default function ErrorPage({ status }: { status: number }) {
    const { title, description } = errorMessages[status] ?? fallback;

    return (
        <>
            <Head title={`${status} - ${title}`} />
            <Navigation />
            <div className="container flex min-h-[60vh] flex-col items-center justify-center text-center">
                <p className="kicker">Errore {status}</p>
                <h1 className="page__title my-2">{title}</h1>
                <p className="max-w-md text-mercury-500">{description}</p>
                <AppLink href="/" className="button__primary mt-8">
                    Torna alla homepage
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </AppLink>
            </div>
            <Colophon />
        </>
    );
}
