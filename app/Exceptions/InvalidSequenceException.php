<?php

namespace App\Exceptions;

use Exception;

class InvalidSequenceException extends Exception
{
    public function report() {
        abort(400, $this->getMessage());
    }
}
