<?php

namespace Scwar\Monnify\Exceptions;

class RequestException extends MonnifyException
{
    /**
     * The response data.
     *
     * @var array
     */
    protected $response;

    /**
     * Create a new request exception instance.
     *
     * @param  string  $message
     * @param  int  $code
     * @param  array  $response
     * @param  \Throwable|null  $previous
     * @return void
     */
    public function __construct($message = 'Request failed', $code = 400, $response = [], $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->response = $response;
    }

    /**
     * Get the response data.
     *
     * @return array
     */
    public function getResponse()
    {
        return $this->response;
    }
}
