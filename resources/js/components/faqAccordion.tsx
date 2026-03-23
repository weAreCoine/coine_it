import { clsx } from 'clsx';
import { useState } from 'react';
import BordersDecorations from '@/components/bordersDecorations';
import DevLabel from '@/components/devLabel';
import Faq = App.Entities.Faq;

type FaqAccordionProps = {
    faqs: Faq[];
    bg?: string;
    bordersDecorations?: boolean;
};

export default function FaqAccordion({ faqs, bg = 'bg-mercury-50', bordersDecorations = true }: FaqAccordionProps) {
    const [openIndex, setOpenIndex] = useState<number | null>(null);

    const faqSchema = {
        '@context': 'https://schema.org',
        '@type': 'FAQPage',
        mainEntity: faqs.map((faq) => ({
            '@type': 'Question',
            name: faq.question,
            acceptedAnswer: {
                '@type': 'Answer',
                text: faq.answer,
            },
        })),
    };

    return (
        <div className="relative grid grid-cols-1 gap-px bg-mercury-200 p-px">
            <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }} />
            <DevLabel name="FaqAccordion" />
            {bordersDecorations && <BordersDecorations bg={bg} />}
            {faqs.map((faq, index) => (
                <div key={index} className={`${bg} p-4`}>
                    <button
                        type="button"
                        onClick={() => setOpenIndex(openIndex === index ? null : index)}
                        className={clsx({
                            'flex w-full cursor-pointer items-center justify-between py-5 text-left font-medium duration-300': true,
                            'text-black': openIndex === index,
                            'text-mercury-400': openIndex !== index,
                        })}
                    >
                        {faq.question}
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            strokeWidth={1.5}
                            stroke="currentColor"
                            className={`size-4 shrink-0 transition-transform duration-300 ${openIndex === index ? 'rotate-180' : ''}`}
                        >
                            <path strokeLinecap="round" strokeLinejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div
                        className={`grid transition-[grid-template-rows] duration-300 ease-in-out ${openIndex === index ? 'grid-rows-[1fr]' : 'grid-rows-[0fr]'}`}
                    >
                        <div className="overflow-hidden">
                            <p className="pb-5 text-sm text-mercury-500">{faq.answer}</p>
                        </div>
                    </div>
                </div>
            ))}
        </div>
    );
}
