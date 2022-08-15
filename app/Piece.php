<?php

namespace App;

interface PieceInterface {
	public function getColor() : bool;
	public function getColorStr() : string;
}

class Piece implements PieceInterface {
	/**
	 * $color -> 0 = "Red", 1 = "Blue"
	 */
	protected $color;

	/**
	 * Constructor for a game piece. Color is specified;
	 */
	public function __construct (bool $color = false) {
		$this->color = $color;
	}

	/**
	 * Returns the piece color as a boolean.
	 */
	public function getColor() : bool {
		return $this->color;
	}

	/**
	 * Returns the piece color as a string.
	 */
	public function getColorStr() : string {
		return $this->color ? "Blue" : "Red";
	}
}

?>
