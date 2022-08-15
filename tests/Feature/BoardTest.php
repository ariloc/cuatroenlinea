<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Board;

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
		$board = new Board(1,1);
		$board = new Board(10,1);
		$board = new Board(1,10);
		$board = new Board(10,10);
	}

	public function test_invalid_dimensions() {
		$this->expectExceptionMessage('Board dimensions must be positive.');
		$board = new Board(-1,1);
		
		$this->expectExceptionMessage('Board dimensions must be positive.');
		$board = new Board(1,-1);
		
		$this->expectExceptionMessage('Board dimensions must be positive.');
		$board = new Board(-1,-1);
	}
	
    public function test_dimx_1()
    {
		$board = new Board(1,10);

		$board->throwPiece(Piece(0), 0);

		$this->expectExceptionMessage('Column index out of range.');
		$board->throwPiece(Piece(0), 1);
		
		$this->expectExceptionMessage('Column index out of range.');
		$board->throwPiece(Piece(0), -1);
    }
}
