import { clsx } from 'clsx';
import { useReducer } from 'react';
import DevLabel from '@/components/devLabel';
import type { QuizConfig, QuizQuestion, ResultRange } from '@/types/dto/healthCheck';

type QuizState = {
    step: number;
    answers: Record<string, { value: string; points: number }>;
    contact: { firstName: string; lastName: string; email: string; url: string };
    openText: string;
    marketingConsent: boolean;
};

type QuizAction =
    | { type: 'SELECT_OPTION'; key: string; value: string; points: number }
    | { type: 'NEXT' }
    | { type: 'PREV' }
    | { type: 'SET_CONTACT'; field: keyof QuizState['contact']; value: string }
    | { type: 'SUBMIT' }
    | { type: 'HIDE_TRANSITION' }
    | { type: 'SET_OPEN_TEXT'; value: string }
    | { type: 'SET_MARKETING_CONSENT'; value: boolean };

function quizReducer(state: QuizState, action: QuizAction): QuizState {
    switch (action.type) {
        case 'SELECT_OPTION':
            return { ...state, answers: { ...state.answers, [action.key]: { value: action.value, points: action.points } } };
        case 'HIDE_TRANSITION':
            return { ...state, step: state.step + 1 };
        case 'PREV':
            return { ...state, step: Math.max(0, state.step - 1) };
        case 'SET_CONTACT':
            return { ...state, contact: { ...state.contact, [action.field]: action.value } };
        case 'SUBMIT':
            return { ...state, step: 7 };
        case 'SET_OPEN_TEXT':
            return { ...state, openText: action.value };
        case 'SET_MARKETING_CONSENT':
            return { ...state, marketingConsent: action.value };
        default:
            return state;
    }
}

function computeScore(questions: QuizQuestion[], answers: QuizState['answers']): number {
    return questions.filter((q) => q.scored).reduce((sum, q) => sum + (answers[q.key]?.points ?? 0), 0);
}

function findRange(score: number, ranges: ResultRange[]): ResultRange {
    return ranges.find((r) => score >= r.min && score <= r.max) ?? ranges[ranges.length - 1];
}

type Finding = { color: 'r' | 'g' | 'a'; title: string; description: string };

const AREA_LABELS: Record<string, string> = {
    advertising: 'la gestione advertising',
    coordination: 'il coordinamento tra marketing e sito',
    tracking: 'il setup di tracciamento dati',
    mobile: "l'esperienza mobile",
    retention: 'la fidelizzazione clienti',
};

function buildFindings(questions: QuizQuestion[], answers: QuizState['answers'], config: QuizConfig): Finding[] {
    const scoredQuestions = questions.filter((q) => q.scored && q.finding);

    const negatives: { question: QuizQuestion; weight: number }[] = [];
    const positives: { question: QuizQuestion; weight: number }[] = [];

    for (const q of scoredQuestions) {
        const score = answers[q.key]?.points ?? 0;
        const f = q.finding!;

        if (score <= f.threshold_max) {
            negatives.push({ question: q, weight: q.weight });
        }
        if (score >= f.threshold_min) {
            positives.push({ question: q, weight: q.weight });
        }
    }

    negatives.sort((a, b) => b.weight - a.weight);
    positives.sort((a, b) => b.weight - a.weight);

    const findings: Finding[] = [];

    if (negatives.length === 0) {
        // Score molto alto: 2 positivi + finding generico
        for (const p of positives.slice(0, 2)) {
            findings.push({ color: 'g', title: p.question.finding!.positive_title, description: p.question.finding!.positive_text });
        }
        findings.push({ color: 'a', title: config.fallbackFinding.title, description: config.fallbackFinding.text });
    } else {
        // Max 2 negativi
        for (const n of negatives.slice(0, 2)) {
            findings.push({ color: 'r', title: n.question.finding!.negative_title, description: n.question.finding!.negative_text });
        }

        // 1 positivo
        if (positives.length > 0) {
            const p = positives[0];
            findings.push({ color: 'g', title: p.question.finding!.positive_title, description: p.question.finding!.positive_text });
        } else {
            // Fallback: area col ratio score/weight più alto
            let bestRatio = -1;
            let bestQ: QuizQuestion | null = null;
            for (const q of scoredQuestions) {
                const ratio = (answers[q.key]?.points ?? 0) / q.weight;
                if (ratio > bestRatio) {
                    bestRatio = ratio;
                    bestQ = q;
                }
            }
            if (bestQ) {
                const areaLabel = AREA_LABELS[bestQ.key] ?? bestQ.key;
                findings.push({
                    color: 'g',
                    title: 'Il tuo punto di forza',
                    description: `L'area dove sei più avanti è ${areaLabel} — un buon punto di partenza su cui costruire.`,
                });
            }
        }
    }

    return findings;
}

function FindingDot({ color }: { color: 'r' | 'a' | 'g' }) {
    return (
        <span
            className={clsx('mt-1.5 inline-block size-2.5 shrink-0 rounded-full', {
                'bg-red-500': color === 'r',
                'bg-amber-500': color === 'a',
                'bg-green-500': color === 'g',
            })}
        />
    );
}

type HealthCheckQuizProps = {
    questions: QuizQuestion[];
    config: QuizConfig;
};

export default function HealthCheckQuiz({ questions, config }: HealthCheckQuizProps) {
    const [state, dispatch] = useReducer(quizReducer, {
        step: 0,
        answers: {},
        contact: { firstName: '', lastName: '', email: '', url: '' },
        openText: '',
        marketingConsent: false,
    });

    const totalQuestions = questions.length;
    const progressPercent = state.step < totalQuestions ? Math.round((state.step / totalQuestions) * 100) : 100;

    function handleContinue() {
        dispatch({ type: 'HIDE_TRANSITION' });
    }

    return (
        <section id="health-check-quiz" aria-labelledby="healthCheckQuizLabel" className="relative bg-black py-24 text-white">
            <DevLabel name="HealthCheckQuiz" />
            <div className="container">
                <div className="mb-12 text-center">
                    <p className="kicker mb-2 text-white/40">Strumento gratuito</p>
                    <h2 id="healthCheckQuizLabel" className="section__title text-white">
                        E-commerce <em>Health Check</em>
                    </h2>
                    <p className="mx-auto max-w-xl text-balance text-white/60">
                        Sei domande sul tuo e-commerce. Al termine ricevi una diagnosi preliminare che identifica le aree di attenzione e le priorità
                        di intervento — senza impegno.
                    </p>
                </div>

                {/* Progress bar — solo durante le domande */}
                {state.step < totalQuestions && (
                    <div className="mx-auto mb-12 max-w-2xl">
                        <p className="mb-2 text-right text-xs text-white/40">{progressPercent}%</p>
                        <div className="h-px bg-white/10">
                            <div
                                className="h-full bg-white transition-all duration-500 ease-out"
                                style={{ width: `${progressPercent}%` }}
                            />
                        </div>
                    </div>
                )}

                <div className="mx-auto max-w-2xl">
                    {/* Question steps (0–5) */}
                    {state.step < totalQuestions && (
                        <QuestionStep
                            question={questions[state.step]}
                            selectedValue={state.answers[questions[state.step].key]?.value}
                            onSelect={(value, points) => dispatch({ type: 'SELECT_OPTION', key: questions[state.step].key, value, points })}
                            onNext={handleContinue}
                            onPrev={() => dispatch({ type: 'PREV' })}
                            isFirst={state.step === 0}
                        />
                    )}

                    {/* Contact step (6) */}
                    {state.step === totalQuestions && (
                        <ContactStep
                            contact={state.contact}
                            marketingConsent={state.marketingConsent}
                            onChange={(field, value) => dispatch({ type: 'SET_CONTACT', field, value })}
                            onMarketingConsentChange={(v) => dispatch({ type: 'SET_MARKETING_CONSENT', value: v })}
                            onSubmit={() => dispatch({ type: 'SUBMIT' })}
                            onPrev={() => dispatch({ type: 'PREV' })}
                        />
                    )}

                    {/* Results step (7) */}
                    {state.step === 7 && (
                        <ResultsStep
                            questions={questions}
                            answers={state.answers}
                            config={config}
                            openText={state.openText}
                            onOpenTextChange={(v) => dispatch({ type: 'SET_OPEN_TEXT', value: v })}
                        />
                    )}
                </div>
            </div>
        </section>
    );
}

function QuestionStep({
    question,
    selectedValue,
    onSelect,
    onNext,
    onPrev,
    isFirst,
}: {
    question: QuizQuestion;
    selectedValue?: string;
    onSelect: (value: string, points: number) => void;
    onNext: () => void;
    onPrev: () => void;
    isFirst: boolean;
}) {
    return (
        <div>
            <h3 className="mb-2 font-display text-2xl">{question.text}</h3>
            <p className="mb-8 text-sm text-white/40">{question.hint}</p>
            <div className="space-y-3">
                {question.options.map((option) => (
                    <button
                        key={option.value}
                        type="button"
                        onClick={() => onSelect(option.value, option.points)}
                        className={clsx(
                            'flex w-full cursor-pointer items-center gap-4 border p-4 text-left transition-colors',
                            selectedValue === option.value ? 'border-white bg-white/10' : 'border-white/10 hover:border-white/20',
                        )}
                    >
                        <span
                            className={clsx(
                                'flex size-5 shrink-0 items-center justify-center rounded-full border',
                                selectedValue === option.value ? 'border-white' : 'border-white/30',
                            )}
                        >
                            {selectedValue === option.value && <span className="size-2.5 rounded-full bg-white" />}
                        </span>
                        <span>{option.label}</span>
                    </button>
                ))}
            </div>
            <div className="mt-8 flex items-center justify-between">
                {!isFirst ? (
                    <button type="button" onClick={onPrev} className="cursor-pointer text-sm text-white/30 transition-colors hover:text-white/60">
                        &larr; Indietro
                    </button>
                ) : (
                    <span />
                )}
                <button
                    type="button"
                    onClick={onNext}
                    disabled={!selectedValue}
                    className={clsx(
                        'cursor-pointer px-6 py-3 text-sm font-medium transition-opacity',
                        selectedValue ? 'bg-white text-black' : 'cursor-not-allowed bg-white/20 text-white/40',
                    )}
                >
                    Continua
                </button>
            </div>
        </div>
    );
}

function ContactStep({
    contact,
    marketingConsent,
    onChange,
    onMarketingConsentChange,
    onSubmit,
    onPrev,
}: {
    contact: QuizState['contact'];
    marketingConsent: boolean;
    onChange: (field: keyof QuizState['contact'], value: string) => void;
    onMarketingConsentChange: (value: boolean) => void;
    onSubmit: () => void;
    onPrev: () => void;
}) {
    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        onSubmit();
    }

    return (
        <form onSubmit={handleSubmit}>
            <h3 className="mb-2 font-display text-2xl">Il tuo punteggio è pronto.</h3>
            <p className="mb-8 text-sm text-white/40">
                Inserisci i tuoi dati per riceverlo insieme a un'analisi personalizzata via email.
            </p>
            <div className="grid grid-cols-1 gap-px bg-mercury-900 p-px md:grid-cols-2">
                <div className="coine__input coine__input--dark">
                    <input
                        id="hc-firstName"
                        type="text"
                        value={contact.firstName}
                        onChange={(e) => onChange('firstName', e.target.value)}
                        placeholder=" "
                        required
                    />
                    <label htmlFor="hc-firstName">
                        Nome <span className="text-white/20">*</span>
                    </label>
                </div>
                <div className="coine__input coine__input--dark">
                    <input
                        id="hc-lastName"
                        type="text"
                        value={contact.lastName}
                        onChange={(e) => onChange('lastName', e.target.value)}
                        placeholder=" "
                        required
                    />
                    <label htmlFor="hc-lastName">
                        Cognome <span className="text-white/20">*</span>
                    </label>
                </div>
                <div className="coine__input coine__input--dark md:col-span-2">
                    <input
                        id="hc-email"
                        type="email"
                        value={contact.email}
                        onChange={(e) => onChange('email', e.target.value)}
                        placeholder=" "
                        required
                    />
                    <label htmlFor="hc-email">
                        Email <span className="text-white/20">*</span>
                    </label>
                </div>
                <div className="coine__input coine__input--dark md:col-span-2">
                    <input
                        id="hc-url"
                        type="url"
                        value={contact.url}
                        onChange={(e) => onChange('url', e.target.value)}
                        placeholder=" "
                        required
                    />
                    <label htmlFor="hc-url">
                        URL del tuo e-commerce <span className="text-white/20">*</span>
                    </label>
                </div>
            </div>
            <div className="mt-8">
                <label className="flex items-start gap-3">
                    <input
                        type="checkbox"
                        checked={marketingConsent}
                        onChange={(e) => onMarketingConsentChange(e.target.checked)}
                        className="mt-0.5 size-4 shrink-0 accent-white"
                    />
                    <span className="text-sm text-white/50">
                        Acconsento al trattamento dei miei dati per ricevere l'analisi e comunicazioni di marketing via email.{' '}
                        <a href="/privacy-policy" target="_blank" className="underline transition-colors hover:text-white/80">
                            Privacy Policy
                        </a>
                        <span className="text-white/30"> *</span>
                    </span>
                </label>
            </div>

            <div className="mt-8 flex items-center justify-between">
                <button type="button" onClick={onPrev} className="cursor-pointer text-sm text-white/30 transition-colors hover:text-white/60">
                    &larr; Indietro
                </button>
                <button
                    type="submit"
                    disabled={!marketingConsent}
                    className={clsx(
                        'px-6 py-3 text-sm font-medium transition-opacity',
                        marketingConsent ? 'cursor-pointer bg-white text-black hover:opacity-90' : 'cursor-not-allowed bg-white/20 text-white/40',
                    )}
                >
                    Visualizza l'analisi
                </button>
            </div>
        </form>
    );
}

function ResultsStep({
    questions,
    answers,
    config,
    openText,
    onOpenTextChange,
}: {
    questions: QuizQuestion[];
    answers: QuizState['answers'];
    config: QuizConfig;
    openText: string;
    onOpenTextChange: (value: string) => void;
}) {
    const score = computeScore(questions, answers);
    const range = findRange(score, config.resultRanges);
    const findings = buildFindings(questions, answers, config);

    return (
        <div>
            <div className="mb-12 text-center">
                {/* Score numerico con colore della fascia */}
                <p className="font-display text-7xl font-bold" style={{ color: range.color }}>
                    {score}
                </p>
                <p className="text-lg text-white/40">/100</p>

                {/* Barra visuale colorata */}
                <div className="mx-auto mt-6 h-2 max-w-md overflow-hidden rounded-full bg-white/10">
                    <div
                        className="h-full rounded-full transition-all duration-700 ease-out"
                        style={{ width: `${score}%`, backgroundColor: range.color }}
                    />
                </div>

                {/* Benchmark */}
                <p className="mx-auto mt-3 max-w-md text-xs text-white/30">{config.benchmarkText}</p>

                {/* Verdict */}
                <p className="mt-6 font-display text-xl">{range.label}</p>
                <p className="mx-auto mt-2 max-w-lg text-sm text-white/60">{range.message}</p>
            </div>

            {/* Findings */}
            <div className="mb-8">
                <h4 className="mb-4 text-sm font-medium tracking-wider text-white/40 uppercase">Aree di attenzione</h4>
                <div className="space-y-4">
                    {findings.map((finding, index) => (
                        <div key={index} className="flex gap-3 border border-white/10 p-4">
                            <FindingDot color={finding.color} />
                            <div>
                                <p className="font-medium">{finding.title}</p>
                                <p className="mt-1 text-sm text-white/60">{finding.description}</p>
                            </div>
                        </div>
                    ))}
                </div>
            </div>

            {/* Blocco motivazionale */}
            <div className="mb-8">
                <h4 className="mb-2 font-display text-lg">{range.motivational_title}</h4>
                <p className="text-sm text-white/60">{range.motivational_text}</p>
            </div>

            {/* Campo aperto */}
            <div className="mb-8">
                <div className="coine__input coine__input--dark">
                    <textarea
                        id="hc-openField"
                        value={openText}
                        onChange={(e) => onOpenTextChange(e.target.value)}
                        placeholder=" "
                        maxLength={config.openField.maxLength}
                        rows={4}
                    />
                    <label htmlFor="hc-openField">{range.open_field_placeholder}</label>
                </div>
                <p className="mt-1 text-right text-xs text-white/30">
                    {openText.length}/{config.openField.maxLength}
                </p>
            </div>

            {/* CTA */}
            <div className="text-center">
                <p className="mx-auto mb-6 max-w-lg text-sm text-white/60">{range.cta_text}</p>
                <button
                    type="button"
                    onClick={() => alert('TODO: Calendly')}
                    className="cursor-pointer bg-white px-8 py-4 font-medium text-black transition-opacity hover:opacity-90"
                >
                    Prenota un incontro gratuito
                </button>
            </div>
        </div>
    );
}
