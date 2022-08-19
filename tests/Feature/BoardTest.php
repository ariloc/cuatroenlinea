<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Board;
use App\Piece;

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
            } catch (\Exception $e) {
                continue;
            }
            $this->fail("No exception thrown with dimx, dimy = " . $try_x[$i] . ", " . $try_y[$i]);
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
                } catch (\Exception $e) {
                    continue;
                }
                $this->fail("No exception thrown with dimx $n, piece thrown at $x");
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
                } catch (\Exception $e) {
                    continue;
                }
                $this->fail("Exception not thrown with nx $nx and ny $ny, on col $pair[0] and row $pair[1]");
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

    public function test_undo()
    {

    }

    // TODO: Test get pieces with somewhat random distributions (and also maybe clean)
    // TODO: Test undo
}
