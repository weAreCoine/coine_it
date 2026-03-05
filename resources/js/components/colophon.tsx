import AppLink from '@/components/appLink';
import DevLabel from '@/components/devLabel';
import { home } from '@/routes';
import { show as contact } from '@/routes/contact';

export default function Colophon() {
    return (
        <div className="colophon relative">
            <DevLabel name="Colophon" />
            <div className="container">
                <div className="flex flex-col justify-between md:flex-row">
                    <AppLink href={home.url()}>
                        <span className="border-mercury-700/60 font-display flex items-center justify-center border-x px-12 py-10 text-3xl font-black">
                            Coiné
                        </span>
                    </AppLink>
                    <AppLink
                        href={contact.url()}
                        className="border-mercury-700/60 bg-mercury-800/20 px-18 hidden items-center justify-center border-x py-10 text-sm font-semibold uppercase md:flex"
                    >
                        Scrivici
                    </AppLink>
                </div>
                <div className="divide-mercury-800 border-mercury-700/60 grid grid-cols-1 divide-x divide-y border md:grid-cols-3 md:text-sm lg:text-base">
                    <div className="flex items-center justify-start gap-4 px-10 py-8">
                        <div className="bg-mercury-800/50 inline-block aspect-square shrink-0 p-4">
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
                    <div className="flex items-center justify-start gap-4 px-10 py-8">
                        <div className="bg-mercury-800/50 inline-block aspect-square shrink-0 p-4">
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
                    <div className="flex items-center justify-start gap-4 px-10 py-8">
                        <div className="bg-mercury-800/50 inline-block aspect-square shrink-0 p-4">
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

                <div className="border-mercury-700/60 bg-mercury-800/20 border-x px-6 py-8">
                    <p className="text-xs">
                        © 2014-{new Date().getFullYear()} <a href="https://coine.it">coine.it</a> - Tutti i diritti riservati // P.IVA: IT03615310368
                    </p>
                </div>
            </div>
        </div>
    );
}
