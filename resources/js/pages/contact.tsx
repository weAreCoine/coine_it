import { Head } from '@inertiajs/react';
import Navigation from '@/components/navigation';
import Colophon from '@/components/colophon';

export default function Contact() {
    return (
        <>
            <Head title="Contact" />
            <Navigation />
            <div className="container my-32 text-center">
                <p className="kicker">Mettiamoci in contatto</p>
                <h1 className="page__title my-2">Contattaci</h1>
                <p className="mx-auto max-w-lg">
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, nunc vel aliquam aliquet, nisl
                    nisl aliquet nisl, vel
                    aliquet nisl nisl vel nisl.
                </p>

                <form action=""></form>
            </div>
            <Colophon />
        </>
    );
}
