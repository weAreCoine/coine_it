import type { AboutData } from '@/types/dto/sections';

export default function About(props: AboutData) {
    return (
        <section className="container my-32">
            <div className="grid grid-cols-1 divide-y divide-mercury-200 border border-mercury-200 lg:grid-cols-12 lg:divide-x lg:divide-y-0">
                <div className="bg-mercury-50 lg:col-span-7">
                    <div className="flex h-full flex-col justify-between px-10 pt-16 pb-8">
                        <div>
                            <p className="kicker">{props.kicker}</p>
                            <p className="mb-2 text-3xl font-semibold">{props.title}</p>
                            <p className="section__content">{props.subtitle}</p>
                        </div>
                        <img src="/svg/dots.svg" alt="" />
                    </div>
                </div>
                <div className="lg:col-span-5">
                    <div className="grid grid-cols-3 divide-x divide-mercury-200 lg:grid-cols-1 lg:grid-rows-3 lg:divide-x-0 lg:divide-y">
                        {props.skills.map((skill, key) => (
                            <div key={key} className="flex items-center gap-4 p-8">
                                <div
                                    className="flex aspect-square items-center justify-center border p-4"
                                    dangerouslySetInnerHTML={{ __html: skill.icon }}
                                ></div>
                                <div>
                                    <p className="text-4xl font-semibold">{skill.scalar}</p>
                                    <p>{skill.description}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                    <a href={props.link.href} title={props.link.title} className="button__primary relative flex py-6 text-center">
                        <span> {props.link.title}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
    );
}
