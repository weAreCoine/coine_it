import AppLink from '@/components/appLink';
import DevLabel from '@/components/devLabel';
import { cookiePolicy, home, privacyPolicy } from '@/routes';
import { show as contact } from '@/routes/contact';

interface ColophonProps {
    marginTop?: boolean;
    borderTop?: boolean;
}
export default function Colophon({ marginTop = true, borderTop = false }: ColophonProps) {
    return (
        <div className={`colophon relative ${marginTop ? 'mt-32' : ''} ${borderTop ? 'border-t border-mercury-700/60' : ''}`}>
            <DevLabel name="Colophon" />
            <div className="container">
                <div className="flex flex-col justify-between md:flex-row">
                    <AppLink href={home.url()}>
                        <span className="flex items-center justify-center border-x border-mercury-700/60 px-12 py-10 font-display text-5xl font-black">
                            Coiné
                        </span>
                    </AppLink>
                    <AppLink
                        href={contact.url()}
                        className="hidden items-center justify-center border-x border-mercury-700/60 bg-mercury-800/20 px-18 py-10 text-sm font-semibold uppercase md:flex"
                    >
                        Scrivici
                    </AppLink>
                </div>
                <div className="grid grid-cols-1 gap-px bg-mercury-700/60 p-px md:grid-cols-3 md:text-sm lg:text-base">
                    <div className="flex items-center justify-start gap-4 bg-black px-10 py-8">
                        <div className="inline-block aspect-square shrink-0 bg-mercury-800/50 p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 24 24" fill="none" className="size-6">
                                <path
                                    fillRule="evenodd"
                                    clipRule="evenodd"
                                    d="M0 2H24V4.31556L12.0001 10.861L0 4.31546V2ZM0 6.59364V22H24V6.59373L12.0001 13.1391L0 6.59364Z"
                                    fill="currentColor"
                                ></path>
                            </svg>{' '}
                        </div>
                        <div>
                            <p className="text-mercury-400">Develop</p>
                            <a href="mailto:dev@coine.it" className="font-semibold">
                                dev@coine.it
                            </a>
                        </div>
                    </div>
                    <div className="flex items-center justify-start gap-4 bg-black px-10 py-8">
                        <div className="inline-block aspect-square shrink-0 bg-mercury-800/50 p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 24 24" fill="none" className="size-6">
                                <path
                                    fillRule="evenodd"
                                    clipRule="evenodd"
                                    d="M0 2H24V4.31556L12.0001 10.861L0 4.31546V2ZM0 6.59364V22H24V6.59373L12.0001 13.1391L0 6.59364Z"
                                    fill="currentColor"
                                ></path>
                            </svg>{' '}
                        </div>
                        <div>
                            <p className="text-mercury-400">Marketing</p>
                            <a href="mailto:marketing@coine.it" className="font-semibold">
                                marketing@coine.it
                            </a>
                        </div>
                    </div>
                    <div className="flex items-center justify-start gap-4 bg-black px-10 py-8">
                        <div className="inline-block aspect-square shrink-0 bg-mercury-800/50 p-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" viewBox="0 0 24 24" fill="none" className="size-6">
                                <path
                                    fillRule="evenodd"
                                    clipRule="evenodd"
                                    d="M0 2H24V4.31556L12.0001 10.861L0 4.31546V2ZM0 6.59364V22H24V6.59373L12.0001 13.1391L0 6.59364Z"
                                    fill="currentColor"
                                ></path>
                            </svg>{' '}
                        </div>
                        <div>
                            <p className="text-mercury-400">Content</p>
                            <a href="mailto:content@coine.it" className="font-semibold">
                                content@coine.it
                            </a>
                        </div>
                    </div>
                </div>

                <div className="border-x border-mercury-700/60 bg-mercury-800/20 px-6 py-8">
                    <div className="sm: justify-between sm:flex sm:items-center">
                        <p className="text-xs">
                            © 2014-{new Date().getFullYear().toString().slice(-2)} <a href="https://coine.it">coine.it</a> - Tutti i diritti riservati{' '}
                            <br />
                            P.IVA: IT03615310368 <span className="mx-1 text-mercury-600"></span>
                        </p>
                        <nav>
                            <ul className="mt-2 items-center gap-4 sm:mt-0 sm:flex">
                                <li>
                                    <AppLink
                                        href={privacyPolicy.url()}
                                        className="text-xs text-mercury-400 underline underline-offset-2 transition-colors hover:text-mercury-200"
                                    >
                                        PRIVACY POLICY
                                    </AppLink>
                                </li>
                                <li>
                                    <AppLink
                                        href={cookiePolicy.url()}
                                        className="text-xs text-mercury-400 underline underline-offset-2 transition-colors hover:text-mercury-200"
                                    >
                                        COOKIE POLICY
                                    </AppLink>
                                </li>
                                <li>
                                    <button
                                        type="button"
                                        onClick={() => window.dispatchEvent(new Event('open-consent-settings'))}
                                        className="cursor-pointer text-xs text-mercury-400 underline underline-offset-2 transition-colors hover:text-mercury-200"
                                    >
                                        PREFERENZE GDPR
                                    </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    );
}
