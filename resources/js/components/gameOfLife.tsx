import { clsx } from 'clsx';

export default function GameOfLife() {
    const boardElement: HTMLDivElement | null = document.querySelector('#game-of-life-board');
    const cells: number = 32;
    const frameRate: number = 2;
    const board: boolean[][] = Array.from({ length: cells }, () => Array.from({ length: cells }, () => Math.random() > 0.5));
    let boardWidth: number = boardElement?.clientWidth ?? 0;

    let cellWidth: number = (boardWidth ?? 0) / cells;
    function tick() {
        for (let i = 0; i < cells; i++) {
            for (let j = 0; j < cells; j++) {
                const neighbors = countNeighbors(i, j);
                if (board[i][j]) {
                    if (neighbors < 2 || neighbors > 3) {
                        board[i][j] = false;
                    }
                } else {
                    if (neighbors === 3) {
                        board[i][j] = true;
                    }
                }
            }
        }
    }

    function countNeighbors(x: number, y: number) {
        let neighbors = 0;
        for (let i = Math.max(0, x - 1); i < Math.min(cells - 1, x + 1); i++) {
            for (let j = Math.max(0, y - 1); j < Math.min(cells - 1, y + 1); j++) {
                if (i === x && j === y) {
                    continue;
                }
                if (board[i][j]) {
                    neighbors++;
                }
            }
        }
        return neighbors;
    }
    setInterval(tick, 1000 / frameRate);

    window.onresize = () => {
        boardWidth = boardElement?.clientWidth ?? 0;
        cellWidth = (boardWidth ?? 0) / cells;
    };
    return (
        <div id="game-of-life-board" className="aspect-square">
            {board.map((row, rowIndex) => (
                <div key={rowIndex} className="flex">
                    {row.map((cell, colIndex) => (
                        <div key={colIndex} className="flex aspect-square h-auto items-center justify-center" style={{ width: cellWidth }}>
                            <div
                                className={clsx({
                                    'bg-black': board[rowIndex][colIndex],
                                    'bg-mercury-200': !board[rowIndex][colIndex],
                                    'h-2/3 w-2/3': true,
                                })}
                            ></div>
                        </div>
                    ))}
                </div>
            ))}
        </div>
    );
}
