<?php

namespace App;

interface BoardInterface {
    public function throwPiece (Piece $piece, int $col) : bool;
    public function getPiece (int $col, int $row) : ?Piece; // can return Piece or NULL if empty
    public function clean() : void;
    public function undo() : bool;
    public function getDimX() : int;
    public function getDimY() : int;
}

class Board implements BoardInterface {
    protected array $columns, $history;
    protected int $dim_x, $dim_y;

    /**
     * Constructor for the board. Dimensions are specified and the board is initialized.
     */
    public function __construct (int $dim_x = 7, int $dim_y = 6) {
        if ($dim_x <= 0 || $dim_y <= 0)
            throw new \Exception('Board dimensions must be positive.');

        $this->dim_x = $dim_x;
        $this->dim_y = $dim_y;
        $this->clean(); 
    }
    
    /**
     * Throws a piece in column $col.
     * Returns true is done correctly. Returns false if column is full.
     */
    public function throwPiece (Piece $piece, int $col) : bool {
        $col--; // 1-indexed to 0-indexed
        if ($col < 0 || $col >= $this->dim_x)
            throw new \Exception('Column index out of range.');

        if (count($this->columns[$col]) >= $this->dim_y)
            return false;

        $this->columns[$col][] = $piece;
        $this->history[] = $col;
        return true;
    }

    /**
     * Gets the Piece object in column $col and row $row.
     * If no piece exists in that position, returns NULL.
     */
    public function getPiece (int $col, int $row) : ?Piece {
        $col--; $row--; // To 0-indexed
        if ($col < 0 || $col >= $this->dim_x || $row < 0 || $row >= $this->dim_y)
            throw new \Exception('Column or row index out of range');

        return $this->columns[$col][$row] ?? NULL;
    }

    /**
     * Clean the board with set dim_x and dim_y.
     */
    public function clean() : void {
        for ($i = 0; $i < $this->dim_x; $i++)
            $this->columns[$i] = [];
        $this->history = [];
    }

    /**
     * Undo the last movement.
     */
    public function undo() : bool {
        if (empty($this->history))
            return false;

        array_pop($this->columns[ array_pop($this->history) ]);
        return true;
    }

    /**
     * Get set column count.
     */
    public function getDimX() : int {
        return $this->dim_x;
    }

    /**
     * Get set row count (height).
     */
    public function getDimY() : int {
        return $this->dim_y;
    }
}

?>
