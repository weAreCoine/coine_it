import { useState } from 'react';
import type { ServicesData } from '@/types/dto/sections';

export default function Services(props: ServicesData) {
    const [activeIndex, setActiveIndex] = useState(0);
    const activeService = props.services[activeIndex];

    return (
        <section className="services">
            <div className="services__header">
                <p className="kicker">{props.kicker}</p>
                <h2 className="section__title">{props.title}</h2>
                <p>{props.subtitle}</p>
            </div>

            <div className="services__tabs">
                <div className="services__tab-list">
                    {props.services.map((service, index) => (
                        <button
                            key={index}
                            type="button"
                            onClick={() => setActiveIndex(index)}
                            className={`services__tab ${activeIndex === index ? 'services__tab--active' : ''}`}
                        >
                            {service.tabIcon && <span className="services__tab-icon" dangerouslySetInnerHTML={{ __html: service.tabIcon }} />}
                            <span>{service.tabLabel}</span>
                        </button>
                    ))}
                </div>

                <div className="services__panel">
                    <div className="services__panel-grid">
                        <div>{activeService.icon && <img src={activeService.icon} alt={activeService.title} className="services__panel-icon" />}</div>
                        <div>
                            <h3 className="services__panel-title">{activeService.title}</h3>
                            <div className="rich__text" dangerouslySetInnerHTML={{ __html: activeService.html }} />
                            <div className="services__panel-cta">
                                <a href={activeService.link.href} title={activeService.link.title} className="button__primary relative">
                                    <span>{activeService.link.title}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
