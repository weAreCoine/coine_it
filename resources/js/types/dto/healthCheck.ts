export type HeroPoint = { title: string; description: string };
export type WorkStep = { number: string; title: string; description: string };

export type QuizOption = { label: string; value: string; points: number };
export type QuizQuestion = { id: number; text: string; hint: string; key: string; options: QuizOption[] };

