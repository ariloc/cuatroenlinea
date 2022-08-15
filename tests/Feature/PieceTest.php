<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Piece;

class PieceTest extends TestCase
{
    /**
     * Basic Piece class testing.
     *
     * @return void
     */
    public function test_red()
    {
		$piece = new Piece(0);

		$this->assertEquals($piece->getColor(), false);
		$this->assertEquals($piece->getColorStr(), "Red");
    }
    
	public function test_blue()
    {
		$piece = new Piece(1);

		$this->assertEquals($piece->getColor(), true);
		$this->assertEquals($piece->getColorStr(), "Blue");
    }

	public function test_default_red()
	{
		$piece = new Piece();

		$this->assertEquals($piece->getColor(), false);
		$this->assertEquals($piece->getColorStr(), "Red");
	}
}
