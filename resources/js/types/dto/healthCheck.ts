export type HeroPoint = { title: string; description: string };
export type WorkStep = { number: string; title: string; description: string };

export type QuizOption = { label: string; value: string; points: number };

export type QuizFinding = {
    negative_title: string;
    negative_text: string;
    positive_title: string;
    positive_text: string;
    threshold_max: number;
    threshold_min: number;
};

export type QuizQuestion = {
    id: number;
    text: string;
    hint: string;
    key: string;
    options: QuizOption[];
    scored: boolean;
    weight: number;
    order: number;
    finding: QuizFinding | null;
};

export type ResultRange = {
    min: number;
    max: number;
    color: string;
    label: string;
    message: string;
    cta_text: string;
    motivational_title: string;
    motivational_text: string;
    open_field_placeholder: string;
};

export type QuizConfig = {
    resultRanges: ResultRange[];
    benchmarkScore: number;
    benchmarkText: string;
    transitionMessages: string[];
    fallbackFinding: { title: string; text: string };
    openField: { text: string; placeholder: string; maxLength: number };
    calendlyUrl: string;
};
