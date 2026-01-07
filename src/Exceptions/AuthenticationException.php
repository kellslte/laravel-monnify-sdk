<?php

namespace Scwar\Monnify\Exceptions;

class AuthenticationException extends MonnifyException
{
    /**
     * Create a new authentication exception instance.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  \Throwable|null  $previous
     * @return void
     */
    public function __construct($message = 'Authentication failed', $code = 401, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
