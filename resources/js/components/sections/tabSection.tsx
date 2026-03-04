import { useState } from 'react';
import AppLink from '@/components/appLink';
import type { TabSectionData } from '@/types/dto/sections';

export default function TabSection(props: TabSectionData) {
    const [activeIndex, setActiveIndex] = useState(0);
    const activeService = props.services[activeIndex];

    return (
        <section className="tab-section">
            <div className="tab-section__header">
                <p className="kicker">{props.kicker}</p>
                <h2 className="section__title">{props.title}</h2>
                <p>{props.subtitle}</p>
            </div>

            <div className="tab-section__tabs">
                <div className="tab-section__tab-list">
                    {props.services.map((service, index) => (
                        <button
                            key={index}
                            type="button"
                            onClick={() => setActiveIndex(index)}
                            className={`tab-section__tab ${activeIndex === index ? 'tab-section__tab--active' : ''}`}
                        >
                            {service.tabIcon && <span className="tab-section__tab-icon" dangerouslySetInnerHTML={{ __html: service.tabIcon }} />}
                            <span>{service.tabLabel}</span>
                        </button>
                    ))}
                </div>

                <div className="tab-section__panel">
                    <div className="tab-section__panel-grid">
                        <div>{activeService.icon && <img src={activeService.icon} alt={activeService.title} className="tab-section__panel-icon" />}</div>
                        <div>
                            <h3 className="tab-section__panel-title">{activeService.title}</h3>
                            <div className="rich__text" dangerouslySetInnerHTML={{ __html: activeService.html }} />
                            <div className="tab-section__panel-cta">
                                <AppLink href={activeService.link.href} title={activeService.link.title} className="button__primary relative">
                                    <span>{activeService.link.title}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </AppLink>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    );
}
