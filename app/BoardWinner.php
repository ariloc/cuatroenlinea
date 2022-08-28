<?php

namespace App;

use App\Board;

interface BoardWinnerInterface {
    public function setBoard (Board $board) : void;
    public function getWinner() : ?array;
}

class BoardWinner implements BoardWinnerInterface {
    const CONSECUTIVE_PIECES = 4;
    protected Board $board;

    /**
     * Constructor for the class. A Board can be specified with it.
     */
    public function __construct (Board $board) {
        $this->setBoard($board);
    }

    /**
     * Sets the board to determine the winner from.
     */
    public function setBoard (Board $board) : void {
        $this->board = $board;
    }

    /**
     * Returns an array of [row,column] pairs indicating the winning positions.
     * Returns NULL if there's no winner.
     */
    public function getWinner() : ?array {
        $nx = $this->board->getDimX();
        $ny = $this->board->getDimY();

        $explore = [];

        // bottom-top
        for ($x = 1; $x <= $nx; $x++)
            $explore[] = [[$x, 1], [0, 1]];
        
        // left-right
        for ($y = 1; $y <= $ny; $y++)
            $explore[] = [[1, $y], [1, 0]];

        // -> NE
        for ($y = 1; $y <= $ny; $y++)
            $explore[] = [[1, $y], [1, 1]];
        for ($x = 1; $x <= $nx; $x++)
            $explore[] = [[$x, 1], [1, 1]];

        // -> NW
        for ($y = 1; $y <= $ny; $y++)
            $explore[] = [[$nx, $y], [-1, 1]];
        for ($x = 1; $x <= $nx; $x++)
            $explore[] = [[$x, 1], [-1, 1]];

        foreach ($explore as list(list($st_x, $st_y), list($inc_x, $inc_y))) {
            $winner_pos = $this->_checkWinnerDirection($st_x, $st_y, $inc_x, $inc_y);
            if (!is_null($winner_pos))
                return $winner_pos;
        }

        return NULL;
    }

    private function _checkWinnerDirection (int $st_x, int $st_y, int $inc_x, int $inc_y) : ?array {
        // Anonymous function for counting colors from board, or avoid errors if there's no piece 
        $add_from_board = function (array &$cnt, int $x, int $y, int $add) {
            $act = $this->board->getPiece($x, $y);
            if (!is_null($act))
                $cnt[ $act->getColor() ] += $add;
        };

        $nx = $this->board->getDimX();
        $ny = $this->board->getDimY();

        $explored = [];
        $cnt = [0,0];
        for ($x = $st_x, $y = $st_y; ($x >= 1 && $x <= $nx) && ($y >= 1 && $y <= $ny); $x += $inc_x, $y += $inc_y) {
            $explored[] = [$x, $y];

            $add_from_board($cnt, $x, $y, 1);
            if (count($explored) > self::CONSECUTIVE_PIECES) {
                list($prv_x, $prv_y) = $explored[count($explored) - self::CONSECUTIVE_PIECES - 1];
                $add_from_board($cnt, $prv_x, $prv_y, -1);
            }

            if ($cnt[0] == self::CONSECUTIVE_PIECES || $cnt[1] == self::CONSECUTIVE_PIECES)
                return array_slice($explored, -self::CONSECUTIVE_PIECES);
        }

        return NULL;
    }
}

?>
