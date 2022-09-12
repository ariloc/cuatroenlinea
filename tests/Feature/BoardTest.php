<?php

namespace Tests\Feature;
namespace App;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Exceptions\BoardDimensionsException;
use App\Exceptions\BoardOobException;

class BoardTest extends TestCase
{
    /**
     * Check Board class functionality and behavior.
     *
     * @return void
     */
    public function test_valid_dimensions()
    {
        // Testing exceptions, no assertions used
        $this->expectNotToPerformAssertions();

        // Any exception thrown would lead the test to fail.
        $board = new Board(1,1);
        $board = new Board(10,1);
        $board = new Board(1,10);
        $board = new Board(10,10);
    }

    public function test_invalid_dimensions() {
        $this->expectNotToPerformAssertions();

        $try_x = array(-1,1,-1,0,1,0);
        $try_y = array(1,-1,-1,1,0,0);

        for ($i = 0; $i < count($try_x); $i++) {
            try {
                $board = new Board($try_x[$i], $try_y[$i]);
            } catch (BoardDimensionsException $e) {
                continue;
            }
            $this->fail("Incorrect exception or no exception thrown with dimx, dimy = " . $try_x[$i] . ", " . $try_y[$i]);
        }
    }
    
    public function test_dimx_edges_and_rand()
    {
        $this->expectNotToPerformAssertions();

        $try_n = array(1,2,3,4,5);
        for ($i = 0; $i < 3; $i++)
            $try_n[] = rand(6,20);
        
        foreach ($try_n as $n) {
            $board = new Board($n, 20);

            for ($i = 1; $i <= $n; $i++) {
                try {
                    $board->throwPiece(new Piece(rand(0,1)), $i); // valid positions
                } catch (\Exception $e) {
                    $this->fail("Failed with dimx $n, piece thrown at $i");
                }
            }

            $to_try = array(0, -1, $n+1);
            for ($i = 0; $i < 3; $i++)
                $to_try[] = rand(-50,-2);
            for ($i = 0; $i < 3; $i++)
                $to_try[] = rand($n+2, $n+50);
            
            foreach ($to_try as $x) { // invalid positions
                try {
                    $board->throwPiece(new Piece(rand(0,1)), $x);
                } catch (BoardOobException $e) {
                    continue;
                }
                $this->fail("Incorrect exception or no exception thrown with dimx $n, piece thrown at $x");
            }
        }
    }
    
    public function test_dimy_rand_height()
    {
        $try_y = array(1,2,3,4,5);
        for ($i = 0; $i < 3; $i++)
            $try_y[] = rand(6,20);

        foreach ($try_y as $m) {
            $board = new Board(20, $m);

            $try_cols = array(1, 20);
            for ($i = 0; $i < 3; $i++) {
                $aux = 1;
                while (in_array($aux, $try_cols))
                    $aux = rand(2,19);
                $try_cols[] = $aux;
            }

            foreach ($try_cols as $col) {
                for ($i = 0; $i < $m; $i++)
                    $this->assertTrue($board->throwPiece(new Piece(rand(0,1)), $col));
            }
            $this->assertFalse($board->throwPiece(new Piece(rand(0,1)), $col));
        }
    }

    public function test_get_piece_oob()
    {
        $this->expectNotToPerformAssertions();
    
        $try_nx = array(1,1,1,2,3,2,2,3,1,20);
        $try_ny = array(1,2,3,1,1,2,3,2,20,1);

        for ($i = 0; $i < 3; $i++) { // A few more small boards
            $try_nx[] = rand(2,5);
            $try_ny[] = rand(2,5);
        }

        for ($i = 5; $i < 3; $i++) { // A few bigger boards
            $try_nx[] = rand(6,20);
            $try_ny[] = rand(6,20);
        }

        for ($i = 0; $i < count($try_nx); $i++) {
            $nx = $try_nx[$i]; $ny = $try_ny[$i];
            $board = new Board($nx, $ny);
            
            $try_xy = array(
                [0,1], [1,0], [0,0], [$nx+1,1], [1,$ny+1], [0,$ny+1], [$nx+1,0], [rand(1,$nx),0],
                [rand(1,$nx),$ny+1], [0,rand(1,$ny)], [$nx+1,rand(1,$ny)], [-1,-1], [-1, rand(1,$ny)],
                [rand(1,$nx), -1]
            );

            foreach ($try_xy as $pair) {
                try {
                    $board->getPiece($pair[0], $pair[1]);
                } catch (BoardOobException $e) {
                    continue;
                }
                $this->fail("Incorrect exception or no exception thrown with nx $nx and ny $ny, on col $pair[0] and row $pair[1]");
            }
        }
    }

    public function test_get_pieces_checkerboard()
    {
        $try_nx = array(1,1,2,2,1,20,20);
        $try_ny = array(1,2,1,2,20,1,20);

        for ($t = 0; $t < count($try_nx); $t++) {
            $nx = $try_nx[$t]; $ny = $try_ny[$t];
            $board = new Board($nx, $ny);

            for ($i = 1; $i <= $nx; $i++)
                for ($j = 1; $j <= $ny; $j++)
                    $board->throwPiece(new Piece(($i+$j)%2), $i);

            for ($i = 1; $i <= $nx; $i++)
                for ($j = 1; $j <= $ny; $j++) {
                    $piece = $board->getPiece($i, $j);
                    $this->assertNotNull($piece);
                    $this->assertEquals($piece->getColor(), ($i+$j)%2);
                }
        }
    }

    public function test_get_pieces_rand()
    {
        $try_nx = array(20,19,20);
        $try_ny = array(20,20,19);

        for ($t = 0; $t < count($try_nx); $t++) {
            $nx = $try_nx[$t]; $ny = $try_ny[$t];
            $board = new Board($nx, $ny);

            $game = array_fill(1, $nx, array_fill(1, $ny, NULL));

            for ($i = 1; $i <= $nx; $i++) {
                $col_n = rand(0,$ny);
                for ($j = 1; $j <= $col_n; $j++) {
                    $piece = new Piece(rand(0,1));
                    $board->throwPiece($piece, $i); // throw piece
                    $this->assertEquals($board->getPiece($i, $j), $piece); // check for piece
                    $game[$i][$j] = $piece;
                }
            }

            // check for all pieces
            for ($i = 1; $i <= $nx; $i++)
                for ($j = 1; $j <= $ny; $j++)
                    $this->assertEquals($board->getPiece($i, $j), $game[$i][$j]);           
        }
    }

    public function test_default_empty_board()
    {
        $board = new Board();

        for ($i = 1; $i <= $board->getDimX(); $i++)
            for ($j = 1; $j <= $board->getDimY(); $j++)
                $this->assertEquals($board->getPiece($i, $j), NULL);
    }

    public function test_board_clean()
    {
        $try_nx = array(1,2,1,2,20,1,20);
        $try_ny = array(1,1,2,2,20,20,1);

        for ($i = 0; $i < 5; $i++) {
            $try_nx[] = rand(1,20);
            $try_ny[] = rand(1,20);
        }

        for ($t = 0; $t < count($try_nx); $t++) {
            $nx = $try_nx[$t]; $ny = $try_ny[$t];
            $toThrow = rand(max(0, $nx * $ny - 10), $nx * $ny + 10);
            $board = new Board($nx, $ny);
            
            for ($i = 0; $i < $toThrow; $i++) {
                $board->throwPiece(new Piece(rand(0,1)), rand(1,$nx));
            }

            $board->clean();

            for ($i = 1; $i <= $nx; $i++)
                for ($j = 1; $j <= $ny; $j++)
                    $this->assertEquals($board->getPiece($i, $j), NULL);
        }
    }
    
    public function test_undo_1()
    {
        // using default board and other sizes
        $try_board = array(new Board(), new Board(1,10), new Board(10,1), new Board(1,1));
        for ($i = 0; $i < 3; $i++)
            $try_board[] = new Board(rand(1,20), rand(1,20));

        // iterate boards
        for ($b = 0; $b < count($try_board); $b++) {
            $board = $try_board[$b];

            $nx = $board->getDimX(); $ny = $board->getDimY();
            $total_pieces = $nx * $ny;
    
            // list all movements to fill board
            $movs = [];
            for ($c = 1; $c <= $nx; $c++)
                for ($r = 1; $r <= $ny; $r++)
                    $movs[] = $c;

            // shuffle movements randomly
            shuffle($movs);

            $undos_n = rand(1,20); // amount of undos to do

            // choose columns to throw a Piece and then undo the action
            $undos_per_col = array_fill(1, $nx, 0);
            for ($i = 0; $i < $undos_n; $i++) {
                $selected_col = rand(1, $nx);
                $undos_per_col[$selected_col]++;
            }

            // choose randomly when to perform the throw & undos
            $acc_col = array_fill(1, $nx, 0);
            for ($i = 0; $i < count($movs); $i++) {
                $act = $movs[$i];

                if ($act < 0) continue; // skip if it's a throw & undo movement

                $acc_col[$act]++; // count amount of Piece in each column up to this point

                // all add & undo should be done before the column full
                if ($acc_col[$act] == $ny) { 
                    $cnt = $undos_per_col[$act];
                    $undos_per_col[$act] = 0;

                    // new element can be between start and current position
                    // (before column is full)
                    for ($shift = 0; $shift < $cnt; $shift++) {
                        array_splice($movs, rand(0, $i + $shift), 0, - $act);
                    }
                    
                    // update current index (-1 because "for" will increment it)
                    $i += max(0, $cnt-1); 
                }
            }

            // throw Piece according to movs array
            $col_len = array_fill(1, $nx, 0);
            $game = array_fill(1, $nx, array_fill(1, $ny, NULL));
            for ($i = 0; $i < count($movs); $i++) {
                $col_throw = abs($movs[$i]);

                // Piece color is determined by turn, just like the real game
                $board->throwPiece(new Piece($i % 2), $col_throw);

                // add Piece to game array if it's not to be undone
                if ($movs[$i] > 0)
                    $game[$col_throw][++$col_len[$col_throw]] = new Piece($i % 2);
                else {
                    $board->undo(); // undo movement

                    // check against board without last movement
                    for ($c = 1; $c <= $nx; $c++)
                        for ($r = 1; $r <= $ny; $r++)
                            $this->assertEquals($board->getPiece($c, $r), $game[$c][$r]);
                }
            }
        }
    }

    public function test_undo_oob()
    {
        $try_board = array(new Board(), new Board(1,1), new Board(2,1), new Board(1,2), new Board(20,20), new Board(1,20));
        for ($i = 0; $i < 3; $i++)
            $try_board[] = new Board(rand(1,20), rand(1,20));

        for ($b = 0; $b < count($try_board); $b++) {
            $board = $try_board[$b];

            $nx = $board->getDimX(); $ny = $board->getDimY();
            $total_pieces = $nx * $ny;

            $try_initial = array();
            
            // first 0-5 if possible
            for ($i = 0; $i < min(5, $total_pieces); $i++)
                $try_initial[] = $i;

            // other random initial for bigger boards
            if ($total_pieces > 5)
                for ($i = 0; $i < 3; $i++)
                    $try_initial[] = rand(6, $total_pieces);

            // throw amount of initial pieces, undo more than thrown
            for ($i = 0; $i < count($try_initial); $i++) {
                $throw_n = $try_initial[$i]; // to throw
                $undo_n = rand(1,20); // additional to undo after throws

                $avail_col = array();
                for ($j = 1; $j <= $nx; $j++)
                    $avail_col[] = $j;

                $col_len = array_fill(1, $nx, 0);
                for ($j = 0; $j < $throw_n; $j++) {
                    // select random column not yet filled
                    $throw_idx = rand(0, count($avail_col)-1);
                    $col = $avail_col[$throw_idx];

                    // it should always be possible to throw this Piece
                    $this->assertTrue($board->throwPiece(new Piece(rand(0,1)), $col));

                    // remove column from list of available ones when it's full
                    if ((++$col_len[$col]) >= $ny)
                        array_splice($avail_col, array_search($col, $avail_col), 1);
                }

                // undo all movements
                while ($throw_n--)
                    $this->assertTrue($board->undo());

                // perform additional "oob" undos
                while ($undo_n--)
                    $this->assertFalse($board->undo());
            }
        }
    }
}
