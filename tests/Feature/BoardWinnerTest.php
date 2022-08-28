<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\BoardWinner;
use App\Board;
use App\Piece;

class BoardWinnerTest extends TestCase
{
    public function test_rows_fixed_1() {
        /*
         *  - - - - - - -
         *  - - - - - - -
         *  - - - - - - -
         *  - - - - - - -
         *  - - - - - - -
         *  R R R R - - -
         */

        $board = new Board(7,6);
        for ($i = 1; $i <= 4; $i++) {
            $board->throwPiece(new Piece(0), $i);
        }

        $this->_compareWinners($board, array([1,1], [2,1], [3,1], [4,1]));
    }

    public function test_rows_fixed_2() {
        /*
         *  - - - B B B B
         *  - - - R R R B
         *  - - - B B B R
         *  - - - R R R B
         *  - - - B B B R
         *  - - - R R R B
         */

        $board = new Board(7,6);
        for ($y = 1; $y < $board->getDimY(); $y++)
            for ($x = $board->getDimX()-3; $x <= $board->getDimX()-1; $x++)
                $board->throwPiece(new Piece(($y+1)%2), $x);

        for ($y = 1; $y < $board->getDimY(); $y++)
            $board->throwPiece(new Piece($y % 2), 7);

        for ($x = 4; $x <= 7; $x++)
            $board->throwPiece(new Piece(1), $x);

        $this->_compareWinners($board, array([4,6],[5,6],[6,6],[7,6]));
    }

    public function test_rows_fixed_3() {
        /*
         *  - - - R - - -
         *  - - - B - B -
         *  - - R R R R -
         *  R - B R B R -
         *  R - R B R R -
         *  B B R B B B R
         */

        $arr = array(
            array(NULL, NULL, NULL, 0,    NULL, NULL, NULL),
            array(NULL, NULL, NULL, 1,    NULL, 1,    NULL),
            array(NULL, NULL, 0,    0,    0,    0,    NULL),
            array(0,    NULL, 1,    0,    1,    0,    NULL),
            array(0,    NULL, 0,    1,    0,    0,    NULL),
            array(1,    1,    0,    1,    1,    1,    0   )
        );
        $board = $this->_boardFromArray($arr);
        
        $this->_compareWinners($board, array([3,4],[4,4],[5,4],[6,4]));
    }

    public function test_empty() {
        $board = new Board();
        $board_winner = new BoardWinner($board);
        $this->assertNull($board_winner->getWinner());
    }

    public function test_full_no_winner() {
        /*
         *  B B R R B B R
         *  R R B B R R B
         *  B B R R B B R
         *  R R B B R R B
         *  B B R R B B R
         *  R R B B R R B
         */

        $board = new Board();
        
        for ($x = 1; $x <= $board->getDimX(); $x++)
            for ($y = 1; $y <= $board->getDimY(); $y++)
                $board->throwPiece(new Piece( ((($y-1)/2)%2) xor (($x-1)%2) ), $x);

        $board_winner = new BoardWinner($board);
        $this->assertNull($board_winner->getWinner());
    }

    public function test_cols_fixed_1() {
        /*
         *  - - - - - - -
         *  - - - - - - -
         *  - - - - - - B
         *  - - - - - - B
         *  - - - - - - B
         *  - - - - - - B
         */

        $board = new Board(7,6);
        for ($y = 1; $y <= 4; $y++)
            $board->throwPiece(new Piece(1), 7);
    
        $this->_compareWinners($board, array([7,1],[7,2],[7,3],[7,4]));
    }
    
    public function test_cols_fixed_2() {
        /*
         *  - R B R - - -
         *  - R R R - - -
         *  - R R R - - -
         *  - B R B - - -
         *  - B R B - - -
         *  - B B B - - -
         */

        $board = new Board(7,6);
        for ($i = 0; $i < 3; $i++) {
            $board->throwPiece(new Piece(1), 2);
            $board->throwPiece(new Piece(1), 4);
        }
        for ($i = 0; $i < 3; $i++) {
            $board->throwPiece(new Piece(0), 2);
            $board->throwPiece(new Piece(0), 4);
        }
        
        $board->throwPiece(new Piece(1), 3);
        for ($i = 0; $i < 4; $i++)
            $board->throwPiece(new Piece(0), 3);
        $board->throwPiece(new Piece(0), 3);
    
        $this->_compareWinners($board, array([3,2],[3,3],[3,4],[3,5]));
    }

    public function test_cols_fixed_3() {
        /*
         *  R - - - - - -
         *  R - - B - R -
         *  R - - R - B R
         *  R - B B - B R
         *  B R B B R B R
         *  R R B R B R B
         */

        $arr = array(
            array(0,    NULL, NULL, NULL, NULL, NULL, NULL),
            array(0,    NULL, NULL, 1,    NULL, 0,    NULL),
            array(0,    NULL, NULL, 0,    NULL, 1,    0   ),
            array(0,    NULL, 1,    1,    NULL, 1,    0   ),
            array(1,    1,    1,    1,    0,    1,    0   ),
            array(0,    0,    1,    0,    1,    0,    1   )
        );
        $board = $this->_boardFromArray($arr);
    
        $this->_compareWinners($board, array([1,3],[1,4],[1,5],[1,6]));
    }

    public function test_two_winners_crossing_diagonals() {
        /*
         *  - - - - - - -
         *  - - - - - - -
         *  - - - R - - -
         *  - - R B R - -
         *  - R B B B R -
         *  R R B B B R R
         */

        $board = new Board(7,6);
        
        $arr = array(
            array(NULL, NULL, NULL, NULL, NULL, NULL, NULL),
            array(NULL, NULL, NULL, NULL, NULL, NULL, NULL),
            array(NULL, NULL, NULL, 0,    NULL, NULL, NULL),
            array(NULL, NULL, 0,    1,    0,    NULL, NULL),
            array(NULL, 0,    1,    1,    1,    0,    NULL),
            array(0,    0,    1,    1,    1,    0,    0   )
        );
        $board = $this->_boardFromArray($arr);

        $board_winner = new BoardWinner($board);
        $win = $board_winner->getWinner();

        $posib1 = array([1,1],[2,2],[3,3],[4,4]);
        $posib2 = array([7,1],[6,2],[5,3],[4,4]);

        $this->assertNotNull($win);
        $this->assertTrue(
            empty(array_udiff($win, $posib1, 'self::_arrayOfCoordsCmp')) ||
            empty(array_udiff($win, $posib2, 'self::_arrayOfCoordsCmp')),
        json_encode($win));
    }

    private function _compareWinners (Board $board, ?array $intended_win) {
        $board_winner = new BoardWinner($board);
        $win = $board_winner->getWinner();

        $this->assertNotNull($win);
        $this->assertEmpty(
            array_udiff($win, $intended_win, 'self::_arrayOfCoordsCmp'),
            json_encode($win)
        );
    }

    private function _arrayOfCoordsCmp($lhs, $rhs) {
        return !($lhs == $rhs);
    }
    
    private function _boardFromArray (array $board_arr) {
        $nx = count($board_arr[0]);
        $ny = count($board_arr);

        $board = new Board($nx, $ny);

        // inverted order allows arrays to be visually declared more clearly
        for ($y = $ny-1; $y >= 0; $y--) 
            for ($x = 0; $x < $nx; $x++)
                if (!is_null($board_arr[$y][$x]))
                    $board->throwPiece(new Piece($board_arr[$y][$x]), $x+1);

        return $board;
    }
}
