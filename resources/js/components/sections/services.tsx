import { useState } from 'react';
import type { ServicesData } from '@/types/dto/sections';

export default function Services(props: ServicesData) {
    const [activeIndex, setActiveIndex] = useState(0);
    const activeService = props.services[activeIndex];

    return (
        <section className="container my-32">
            <div className="text-center">
                <p className="kicker">{props.kicker}</p>
                <h2 className="section__title">{props.title}</h2>
                <p>{props.subtitle}</p>
            </div>

            <div className="mx-auto mt-12 max-w-4xl">
                <div className="grid grid-cols-3 items-center">
                    {props.services.map((service, index) => (
                        <button
                            key={index}
                            type="button"
                            onClick={() => setActiveIndex(index)}
                            className={`flex cursor-pointer items-center justify-center gap-2 border px-6 py-5 font-medium transition-colors duration-300 ${
                                activeIndex === index
                                    ? 'border-black bg-mercury-100 text-black'
                                    : 'border-mercury-200 text-mercury-400 hover:text-black'
                            }`}
                        >
                            {service.tabIcon && <span className={`size-4 fill-current`} dangerouslySetInnerHTML={{ __html: service.tabIcon }} />}
                            <span>{service.tabLabel}</span>
                        </button>
                    ))}
                </div>

                <div className="border-x border-b border-mercury-200 bg-mercury-50">
                    <div className="grid grid-cols-2 px-12 py-18">
                        <div>{activeService.icon && <img src={activeService.icon} alt={activeService.title} className="mb-6 h-12 w-12" />}</div>
                        <div>
                            <h3 className="mb-2 text-lg font-medium">{activeService.title}</h3>
                            <div dangerouslySetInnerHTML={{ __html: activeService.html }} />
                        </div>
                    </div>
                </div>
            </div>

            <div className="mt-12 text-center">
                <a href={props.link.href} title={props.link.title} className="button__primary relative">
                    <span>{props.link.title}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </div>
        </section>
    );
}
