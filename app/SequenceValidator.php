<?php

namespace App;

use App\Exceptions\InvalidSequenceException;

interface SequenceValidatorInterface {
    public function validateSequence (string $sequence) : void;
}

class SequenceValidator implements SequenceValidatorInterface {
    const BOARD_COLUMNS = 7;
    const BOARD_ROWS = 6;

    public function validateSequence (string $sequence) : void {
        $seq_arr = str_split($sequence);
        $cnt_per_column = array_fill(1, self::BOARD_COLUMNS, 0);

        foreach ($seq_arr as $x) {
            if (!is_numeric($x))
                throw new InvalidSequenceException("Character '{$x}' is not a valid column number");
            
            if ($x < '1' || $x > self::BOARD_COLUMNS)
                throw new InvalidSequenceException("The column number {$x} is out of range [1," . self::BOARD_COLUMNS . "]");

            $cnt_per_column[intval($x)]++;
        }
        foreach ($cnt_per_column as $x => $cnt)
            if ($cnt > self::BOARD_ROWS)
                throw new InvalidSequenceException("The number of pieces thrown to column {$x} is more than " . self::BOARD_ROWS);
    }
}

?>
