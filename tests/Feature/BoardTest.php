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
		// Will only test whether exceptions are thrown or not
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
/*	
	public function test_dimy_one_column()
	{
		$board = (
	}*/
}
