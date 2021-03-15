<?php

namespace Illuminate\Core;

class Redirect
{
    /**
     * Hold the errors code
     * @var array $_errors
     */
    private $_errors = [];

    public function __construct()
    {
        $this->_errors = [
            301 => 'Moved Permanently',
            307 => 'Temporary Redirect',
            308 => 'Permanent Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            408 => 'Request Timeout',
            415 => 'Unsupported Media Type',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout'
        ];
    }

    /**
     * Handling aborted response code
     * @param int $statusCode
     * @return mixed
     */
    public function abort($statusCode)
    {
        die($statusCode . ' - ' . $this->_errors[$statusCode]);
    }
}
