import { clsx } from 'clsx';
import { useReducer } from 'react';
import DevLabel from '@/components/devLabel';
import type { QuizQuestion } from '@/types/dto/healthCheck';

type QuizState = {
    step: number;
    answers: Record<string, { value: string; points: number }>;
    contact: { firstName: string; lastName: string; email: string; url: string };
};

type QuizAction =
    | { type: 'SELECT_OPTION'; key: string; value: string; points: number }
    | { type: 'NEXT' }
    | { type: 'PREV' }
    | { type: 'SET_CONTACT'; field: keyof QuizState['contact']; value: string }
    | { type: 'SUBMIT' };

function quizReducer(state: QuizState, action: QuizAction): QuizState {
    switch (action.type) {
        case 'SELECT_OPTION':
            return { ...state, answers: { ...state.answers, [action.key]: { value: action.value, points: action.points } } };
        case 'NEXT':
            return { ...state, step: state.step + 1 };
        case 'PREV':
            return { ...state, step: Math.max(0, state.step - 1) };
        case 'SET_CONTACT':
            return { ...state, contact: { ...state.contact, [action.field]: action.value } };
        case 'SUBMIT':
            return { ...state, step: 7 };
        default:
            return state;
    }
}

function calculateScore(answers: QuizState['answers']): number {
    let s = 62;

    const tracking = answers.tracking?.value;
    if (tracking === 'full') s += 15;
    else if (tracking === 'partial') s += 5;
    else if (tracking === 'basic') s -= 5;
    else if (tracking === 'none') s -= 15;

    const checkout = answers.checkout?.value;
    if (checkout === 'good') s += 10;
    else if (checkout === 'bad') s -= 12;
    else if (checkout === 'unknown') s -= 8;

    const ads = answers.ads?.value;
    if (ads === 'agency' || ads === 'freelance') s += 5;
    else if (ads === 'none') s -= 5;

    const pain = answers.pain?.value;
    if (pain === 'data' || pain === 'silos') s -= 8;

    const age = answers.age?.value;
    if (age === 'restructure') s -= 8;
    else if (age === 'mature') s += 5;

    return Math.max(20, Math.min(86, s));
}

type Finding = { color: 'r' | 'a' | 'g'; title: string; description: string };

function generateFindings(answers: QuizState['answers']): Finding[] {
    const findings: Finding[] = [];

    const tracking = answers.tracking?.value;
    if (tracking === 'none' || tracking === 'basic') {
        findings.push({
            color: 'r',
            title: 'Tracking: dati non affidabili',
            description:
                "Ottimizzare le campagne su dati parziali o errati significa prendere decisioni nella direzione sbagliata. Il fix del tracking è la prima cosa da fare — aumenta l'efficacia di ogni altra azione successiva.",
        });
    }

    const checkout = answers.checkout?.value;
    if (checkout === 'bad' || checkout === 'unknown') {
        findings.push({
            color: 'r',
            title: 'Checkout mobile: punto di frizione non monitorato',
            description:
                'Oltre il 60% degli acquisti avviene da smartphone. Un checkout non ottimizzato per mobile può generare abbandoni che non emergono nei report standard.',
        });
    }

    const pain = answers.pain?.value;
    if (pain === 'silos') {
        findings.push({
            color: 'a',
            title: 'Coordinamento tra marketing e sviluppo',
            description:
                'La frammentazione tra chi gestisce le campagne e chi interviene sul sito genera ritardi e ottimizzazioni parziali. È il problema strutturale che affrontiamo più spesso.',
        });
    } else if (pain === 'roas') {
        findings.push({
            color: 'a',
            title: "Ritorno sull'investimento basso o instabile",
            description:
                "Un ROAS basso ha quasi sempre un'origine identificabile — tracking errato, creatività esaurite, landing page non allineata, o problemi nel funnel. L'audit completo serve a isolare la causa prima di intervenire.",
        });
    } else if (pain === 'traffic') {
        findings.push({
            color: 'a',
            title: 'Traffico che non si trasforma in conversioni',
            description:
                "Quando il traffico c'è ma le vendite no, il problema è quasi sempre nel sito — UX, velocità, chiarezza del valore, call to action. Le campagne amplificano ciò che funziona; non compensano ciò che non funziona.",
        });
    } else if (pain === 'data') {
        findings.push({
            color: 'a',
            title: 'Mancanza di segnali chiari su dove intervenire',
            description:
                'Non sapere dove concentrarsi è spesso il sintomo di un setup analytics incompleto o di metriche non prioritizzate. Il primo obiettivo è costruire un quadro leggibile.',
        });
    }

    const age = answers.age?.value;
    if (age === 'restructure') {
        findings.push({
            color: 'a',
            title: 'E-commerce maturo con segnali di stagnazione',
            description:
                "Un progetto con storia che non cresce più richiede spesso un'analisi strutturale — non più campagne o più contenuti, ma una revisione delle fondamenta: funnel, posizionamento, canali.",
        });
    }

    const ads = answers.ads?.value;
    if (ads === 'none') {
        findings.push({
            color: 'a',
            title: 'Advertising non ancora attivo',
            description:
                "Prima di attivare le campagne è importante che il sito sia nella condizione di ricevere e convertire il traffico. L'Health Check completo valuta questo aspetto in modo sistematico.",
        });
    }

    if (findings.length === 0) {
        findings.push({
            color: 'g',
            title: 'Nessuna criticità emergente nelle aree analizzate',
            description:
                "L'audit completo esamina tracking, funnel e benchmark competitivo per identificare le opportunità di ottimizzazione che non emergono dall'analisi preliminare.",
        });
    }

    return findings;
}

function getVerdict(score: number): { verdict: string; description: string } {
    if (score >= 68) {
        return {
            verdict: 'Base solida, margini di crescita identificabili',
            description:
                'Il tuo e-commerce ha fondamenta discrete. Le aree di miglioramento che abbiamo individuato sono principalmente di ottimizzazione, non di ricostruzione.',
        };
    }
    if (score >= 44) {
        return {
            verdict: 'Potenziale inespresso — alcune priorità chiare',
            description:
                'Ci sono nodi che limitano le performance e che sono risolvibili con interventi mirati. Alcune di queste perdite probabilmente non sono ancora visibili nei report standard.',
        };
    }
    return {
        verdict: 'Gap strutturali che richiedono attenzione',
        description:
            'Ci sono aree critiche che stanno probabilmente già influenzando il fatturato. Il punto di partenza è capire dove si concentrano le perdite reali.',
    };
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
};

export default function HealthCheckQuiz({ questions }: HealthCheckQuizProps) {
    const [state, dispatch] = useReducer(quizReducer, {
        step: 0,
        answers: {},
        contact: { firstName: '', lastName: '', email: '', url: '' },
    });

    const totalQuestions = questions.length;
    const progressWidth = state.step <= totalQuestions ? `${(state.step / totalQuestions) * 100}%` : '100%';

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

                {/* Progress bar */}
                <div className="mx-auto mb-12 h-px max-w-2xl bg-white/10">
                    <div className="h-full bg-white transition-all duration-500 ease-out" style={{ width: progressWidth }} />
                </div>

                <div className="mx-auto max-w-2xl">
                    {/* Question steps (0–5) */}
                    {state.step < totalQuestions && (
                        <QuestionStep
                            question={questions[state.step]}
                            selectedValue={state.answers[questions[state.step].key]?.value}
                            onSelect={(value, points) => dispatch({ type: 'SELECT_OPTION', key: questions[state.step].key, value, points })}
                            onNext={() => dispatch({ type: 'NEXT' })}
                            onPrev={() => dispatch({ type: 'PREV' })}
                            isFirst={state.step === 0}
                        />
                    )}

                    {/* Contact step (6) */}
                    {state.step === totalQuestions && (
                        <ContactStep
                            contact={state.contact}
                            onChange={(field, value) => dispatch({ type: 'SET_CONTACT', field, value })}
                            onSubmit={() => dispatch({ type: 'SUBMIT' })}
                            onPrev={() => dispatch({ type: 'PREV' })}
                        />
                    )}

                    {/* Results step (7) */}
                    {state.step === 7 && <ResultsStep answers={state.answers} />}
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
    onChange,
    onSubmit,
    onPrev,
}: {
    contact: QuizState['contact'];
    onChange: (field: keyof QuizState['contact'], value: string) => void;
    onSubmit: () => void;
    onPrev: () => void;
}) {
    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        onSubmit();
    }

    return (
        <form onSubmit={handleSubmit}>
            <h3 className="mb-2 font-display text-2xl">Dove inviamo la tua analisi?</h3>
            <p className="mb-8 text-sm text-white/40">
                La diagnosi è pronta. Inserisci i tuoi dati per riceverla e per prenotare una call di approfondimento gratuita.
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
                    <input id="hc-url" type="url" value={contact.url} onChange={(e) => onChange('url', e.target.value)} placeholder=" " />
                    <label htmlFor="hc-url">URL del tuo e-commerce</label>
                </div>
            </div>
            <div className="mt-8 flex items-center justify-between">
                <button type="button" onClick={onPrev} className="cursor-pointer text-sm text-white/30 transition-colors hover:text-white/60">
                    &larr; Indietro
                </button>
                <button
                    type="submit"
                    className="cursor-pointer bg-white px-6 py-3 text-sm font-medium text-black transition-opacity hover:opacity-90"
                >
                    Visualizza l'analisi
                </button>
            </div>
        </form>
    );
}

function ResultsStep({ answers }: { answers: QuizState['answers'] }) {
    const score = calculateScore(answers);
    const { verdict, description } = getVerdict(score);
    const findings = generateFindings(answers);

    return (
        <div>
            <div className="mb-12 text-center">
                <p className="font-display text-7xl font-bold">{score}</p>
                <p className="text-lg text-white/40">punteggio / 100</p>
                <p className="mt-4 font-display text-xl">{verdict}</p>
                <p className="mx-auto mt-2 max-w-lg text-sm text-white/60">{description}</p>
            </div>

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

            <p className="mb-8 text-center text-xs text-white/30">
                Questa è un'analisi preliminare basata sulle tue risposte. L'audit completo include un'analisi del tracking, una revisione del funnel,
                un benchmark competitivo e un piano d'azione prioritizzato — con i dati reali del tuo sito.
            </p>

            <div className="text-center">
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
