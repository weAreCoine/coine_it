import { clsx } from 'clsx';
import AppLink from '@/components/appLink';
import DevLabel from '@/components/devLabel';
import type { FeaturePromoData } from '@/types/dto/sections';

export default function FeaturePromo({ kicker, title, subtitle, image, imageAlt, link, badge, bullets, theme = 'light' }: FeaturePromoData) {
    const isDark = theme === 'dark';

    return (
        <section
            className={clsx({
                'bg-black text-white': isDark,
                'bg-mercury-50 text-black': !isDark,
                'py-20': true,
            })}
        >
            <DevLabel name="FeaturePromo" />
            <div className="relative container my-20 grid grid-cols-1 items-center gap-12 md:grid-cols-2">
                <div className="aspect-square">
                    <img src={image} alt={imageAlt} className="h-full w-full object-cover object-top md:object-center" loading="lazy" />
                </div>
                <div className="flex flex-col gap-4">
                    {badge && (
                        <span
                            className={`inline-flex w-fit items-center px-3 py-1 text-xs font-semibold tracking-wide uppercase ${
                                isDark ? 'bg-white text-black' : 'bg-black text-white'
                            }`}
                        >
                            {badge}
                        </span>
                    )}
                    <p className="kicker">{kicker}</p>
                    <h2 className="section__title">{title}</h2>
                    <p className={`text-balance ${isDark ? 'text-mercury-200' : 'text-mercury-500'}`}>{subtitle}</p>
                    {bullets && bullets.length > 0 && (
                        <ul className="mt-2 flex flex-col gap-2">
                            {bullets.map((bullet) => (
                                <li key={bullet} className="flex items-start gap-2 text-sm">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                        className="mt-0.5 size-5 shrink-0"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.5 7.55a1 1 0 0 1-1.42.005l-3.5-3.5a1 1 0 1 1 1.42-1.42l2.79 2.79 6.79-6.84a1 1 0 0 1 1.414.001Z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                    <span>{bullet}</span>
                                </li>
                            ))}
                        </ul>
                    )}
                    <div className="mt-4">
                        <AppLink href={link.href} title={link.title} className={`button__primary ${isDark ? 'bg-white text-black' : ''}`}>
                            <span>{link.title}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </AppLink>
                    </div>
                </div>
            </div>
        </section>
    );
}
