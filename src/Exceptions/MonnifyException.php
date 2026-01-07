<?php

namespace Scwar\Monnify\Exceptions;

use Exception;

class MonnifyException extends Exception
{
    /**
     * Create a new Monnify exception instance.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  \Throwable|null  $previous
     * @return void
     */
    public function __construct($message = '', $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
