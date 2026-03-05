import { clsx } from 'clsx';
import { useCallback, useEffect, useRef, useState } from 'react';
import DevLabel from '@/components/devLabel';

const CELLS = 32;
const FRAME_RATE = 2.5;

function createBoard(): boolean[][] {
    return Array.from({ length: CELLS }, () => Array.from({ length: CELLS }, () => Math.random() > 0.5));
}

function countNeighbors(board: boolean[][], x: number, y: number): number {
    let neighbors = 0;
    for (let i = Math.max(0, x - 1); i <= Math.min(CELLS - 1, x + 1); i++) {
        for (let j = Math.max(0, y - 1); j <= Math.min(CELLS - 1, y + 1); j++) {
            if (i === x && j === y) continue;
            if (board[i][j]) neighbors++;
        }
    }
    return neighbors;
}

function nextGeneration(board: boolean[][]): boolean[][] {
    return board.map((row, i) =>
        row.map((cell, j) => {
            const neighbors = countNeighbors(board, i, j);
            if (cell) return neighbors === 2 || neighbors === 3;
            return neighbors === 3;
        }),
    );
}

export default function GameOfLife() {
    const containerRef = useRef<HTMLDivElement | null>(null);
    const [board, setBoard] = useState(createBoard);
    const [cellWidth, setCellWidth] = useState(0);
    const [isPlaying, setIsPlaying] = useState(true);
    const [showInfo, setShowInfo] = useState(false);
    const isDragging = useRef(false);
    const paintValue = useRef(false);

    useEffect(() => {
        const updateCellWidth = () => {
            if (containerRef.current) {
                setCellWidth(containerRef.current.clientWidth / CELLS);
            }
        };

        updateCellWidth();
        window.addEventListener('resize', updateCellWidth);

        return () => window.removeEventListener('resize', updateCellWidth);
    }, []);

    useEffect(() => {
        if (!isPlaying) return;

        const interval = setInterval(() => {
            setBoard((prev) => nextGeneration(prev));
        }, 1000 / FRAME_RATE);

        return () => clearInterval(interval);
    }, [isPlaying]);

    const setCell = (row: number, col: number, value: boolean) => {
        setBoard((prev) => prev.map((r, i) => (i === row ? r.map((c, j) => (j === col ? value : c)) : r)));
    };

    const handleMouseDown = (row: number, col: number) => {
        if (isPlaying) return;
        isDragging.current = true;
        paintValue.current = !board[row][col];
        setCell(row, col, paintValue.current);
    };

    const handleMouseEnter = (row: number, col: number) => {
        if (isPlaying || !isDragging.current) return;
        setCell(row, col, paintValue.current);
    };

    const getCellFromPoint = useCallback(
        (clientX: number, clientY: number): { row: number; col: number } | null => {
            if (!containerRef.current || cellWidth === 0) return null;
            const rect = containerRef.current.getBoundingClientRect();
            const col = Math.floor((clientX - rect.left) / cellWidth);
            const row = Math.floor((clientY - rect.top) / cellWidth);
            if (row < 0 || row >= CELLS || col < 0 || col >= CELLS) return null;
            return { row, col };
        },
        [cellWidth],
    );

    const lastTouchedCell = useRef<string | null>(null);

    const handleTouchStart = (e: React.TouchEvent, row: number, col: number) => {
        if (isPlaying) return;
        e.preventDefault();
        isDragging.current = true;
        paintValue.current = !board[row][col];
        lastTouchedCell.current = `${row},${col}`;
        setCell(row, col, paintValue.current);
    };

    const handleTouchMove = (e: React.TouchEvent) => {
        if (isPlaying || !isDragging.current) return;
        e.preventDefault();
        const touch = e.touches[0];
        const cell = getCellFromPoint(touch.clientX, touch.clientY);
        if (!cell) return;
        const key = `${cell.row},${cell.col}`;
        if (key === lastTouchedCell.current) return;
        lastTouchedCell.current = key;
        setCell(cell.row, cell.col, paintValue.current);
    };

    useEffect(() => {
        const handlePointerUp = () => {
            isDragging.current = false;
            lastTouchedCell.current = null;
        };

        window.addEventListener('mouseup', handlePointerUp);
        window.addEventListener('touchend', handlePointerUp);

        return () => {
            window.removeEventListener('mouseup', handlePointerUp);
            window.removeEventListener('touchend', handlePointerUp);
        };
    }, []);

    return (
        <div className="relative">
            <DevLabel name="GameOfLife" />
            <div className="relative">
                <div ref={containerRef} className="border-2 border-transparent" style={!isPlaying ? { touchAction: 'none' } : undefined} onTouchMove={handleTouchMove}>
                    {board.map((row, rowIndex) => (
                        <div key={rowIndex} className="flex">
                            {row.map((cell, colIndex) => (
                                <div
                                    key={colIndex}
                                    className={clsx('flex aspect-square h-auto items-center justify-center', {
                                        'cursor-pointer': !isPlaying,
                                    })}
                                    style={{ width: cellWidth }}
                                    onMouseDown={() => handleMouseDown(rowIndex, colIndex)}
                                    onMouseEnter={() => handleMouseEnter(rowIndex, colIndex)}
                                    onTouchStart={(e) => handleTouchStart(e, rowIndex, colIndex)}
                                >
                                    <div
                                        className={clsx('h-2/3 w-2/3', {
                                            'bg-black': cell,
                                            'bg-mercury-200': !cell,
                                        })}
                                    />
                                </div>
                            ))}
                        </div>
                    ))}
                </div>
                {showInfo && (
                    <div className="absolute inset-0 flex flex-col justify-center gap-4 border-2 border-black bg-white/80 backdrop-blur-sm p-6 text-sm leading-relaxed">
                        <p>
                            <strong>Game of Life</strong> è un automa cellulare ideato dal matematico John Conway nel 1970. È un gioco a
                            zero giocatori: la sua evoluzione è determinata dallo stato iniziale, senza ulteriori input.
                        </p>
                        <p>L'universo è una griglia di celle, ciascuna viva o morta. Ad ogni generazione, le celle evolvono secondo tre regole:</p>
                        <ul className="list-inside list-disc space-y-1">
                            <li>Una cella viva con 2 o 3 vicini sopravvive.</li>
                            <li>Una cella morta con esattamente 3 vicini diventa viva.</li>
                            <li>Tutte le altre celle muoiono o restano morte.</li>
                        </ul>
                        <p>Metti in pausa la simulazione per disegnare i tuoi pattern, poi premi play per vederli evolvere.</p>
                    </div>
                )}
            </div>
            <div className="mt-2 flex justify-between">
                <div className="flex gap-2">
                    <button onClick={() => setShowInfo((prev) => !prev)} className="game-of-life__button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 512">
                            <path d="M48 80a48 48 0 1 1 96 0A48 48 0 1 1 48 80zM0 224c0-17.7 14.3-32 32-32l64 0c17.7 0 32 14.3 32 32l0 224 32 0c17.7 0 32 14.3 32 32s-14.3 32-32 32L32 512c-17.7 0-32-14.3-32-32s14.3-32 32-32l32 0 0-192-32 0c-17.7 0-32-14.3-32-32z" />
                        </svg>
                    </button>
                </div>
                <div className="flex gap-2">
                <button onClick={() => setIsPlaying((prev) => !prev)} className="game-of-life__button">
                    {isPlaying ? (
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                            <path d="M48 64C21.5 64 0 85.5 0 112L0 400C0 426.5 21.5 448 48 448L112 448C138.5 448 160 426.5 160 400L160 112C160 85.5 138.5 64 112 64L48 64zM208 112L208 400C208 426.5 229.5 448 256 448L320 448C346.5 448 368 426.5 368 400L368 112C368 85.5 346.5 64 320 64L256 64C229.5 64 208 85.5 208 112z" />
                        </svg>
                    ) : (
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512">
                            <path d="M73 39c-14.8-9.1-33.4-9.4-48.5-.9S0 62.6 0 80L0 432c0 17.4 9.4 33.4 24.5 41.9s33.7 8.1 48.5-.9L361 297c14.3-8.8 23-24.2 23-41s-8.7-32.2-23-41L73 39z" />
                        </svg>
                    )}
                </button>
                {!isPlaying && (
                    <button onClick={() => setBoard(Array.from({ length: CELLS }, () => Array(CELLS).fill(false)))} className="game-of-life__button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512">
                            <path d="M290.7 57.4L57.4 290.7c-25 25-25 65.5 0 90.5l80 80c12 12 28.3 18.7 45.3 18.7L288 480l9.4 0L512 480c17.7 0 32-14.3 32-32s-14.3-32-32-32l-124.1 0L518.6 285.3c25-25 25-65.5 0-90.5L381.3 57.4c-25-25-65.5-25-90.5 0zM297.4 416l-9.4 0-105.4 0-80-80L227.3 211.3 364.7 348.7 297.4 416z" />
                        </svg>
                    </button>
                )}
                <button onClick={() => setBoard(createBoard)} className="game-of-life__button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640">
                        <path d="M88 256L232 256C241.7 256 250.5 250.2 254.2 241.2C257.9 232.2 255.9 221.9 249 215L202.3 168.3C277.6 109.7 386.6 115 455.8 184.2C530.8 259.2 530.8 380.7 455.8 455.7C380.8 530.7 259.3 530.7 184.3 455.7C174.1 445.5 165.3 434.4 157.9 422.7C148.4 407.8 128.6 403.4 113.7 412.9C98.8 422.4 94.4 442.2 103.9 457.1C113.7 472.7 125.4 487.5 139 501C239 601 401 601 501 501C601 401 601 239 501 139C406.8 44.7 257.3 39.3 156.7 122.8L105 71C98.1 64.2 87.8 62.1 78.8 65.8C69.8 69.5 64 78.3 64 88L64 232C64 245.3 74.7 256 88 256z" />
                    </svg>
                </button>
                </div>
            </div>
        </div>
    );
}
