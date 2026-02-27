import type { GetInTouchData } from '@/types/dto/sections';

export default function GetInTouch(props: GetInTouchData) {
    return (
        <div className="my-32 bg-black pt-10 text-white">
            <div className="relative">
                <div className="absolute bottom-0 left-0 h-full w-full text-mercury-400">
                    <img src="/svg/mountains.svg" alt="decoration" className="h-full w-auto object-cover" />
                </div>
                <div className="relative container pt-24 pb-48">
                    <div className="max-w-lg">
                        <p className="kicker">{props.kicker}</p>
                        <h2 className="section__title">{props.title}</h2>
                        <p className="text-balance">{props.subtitle}</p>
                        <div className="mt-8">
                            <a href={props.link.href} title={props.link.title} className="button__primary relative bg-white text-black">
                                <span> {props.link.title}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
