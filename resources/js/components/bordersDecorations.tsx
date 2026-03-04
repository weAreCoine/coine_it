type BordersDecorationsProps = {
    bg?: string;
};

export default function BordersDecorations({ bg = 'bg-white' }: BordersDecorationsProps) {
    return (
        <>
            <div className="absolute right-full bottom-full h-20 w-32 translate-x-px translate-y-px bg-linear-to-br from-transparent to-mercury-200">
                <div className={`absolute right-px bottom-px h-full w-full ${bg}`}></div>
            </div>
            <div className="absolute bottom-full left-full h-24 w-18 -translate-x-px translate-y-px bg-linear-to-bl from-transparent from-40% to-mercury-200">
                <div className={`absolute bottom-px left-px h-full w-full ${bg}`}></div>
            </div>
            <div className="absolute top-full right-full h-16 w-20 translate-x-px -translate-y-px bg-linear-to-tr from-transparent to-mercury-200">
                <div className={`absolute top-px right-px h-full w-full ${bg}`}></div>
            </div>
            <div className="absolute top-full left-full h-20 w-36 -translate-x-px -translate-y-px bg-linear-to-tl from-transparent to-mercury-200">
                <div className={`absolute top-px left-px h-full w-full ${bg}`}></div>
            </div>
        </>
    );
}
