<?php

namespace Illuminate\Core;

class Response
{
    private $_responseStatus;

    public function __construct()
    {
        $this->_responseStatus = http_response_code();
    }

    public function header($name, $value)
    {
        header("{$name}: {$value}");
        return $this;
    }

    public function html($response)
    {
        $this->header('Content-Length', strlen($response));
        $this->header('Content-Type', 'text/html');

        echo $response;
    }

    public function json($response)
    {
        $response = json_encode($response, JSON_PRETTY_PRINT);

        $this->header('Content-Length', strlen($response));
        $this->header('Content-Type', 'application/json');

        echo $response;
    }

    public function redirect($url)
    {
        $this->status(302);
        header("Location: {$url}");
        return $this;
    }

    public function status($statusCode = null)
    {
        if (is_null($statusCode)) {
            return $this->_responseStatus;
        }

        $this->_responseStatus = http_response_code($statusCode);
        return $this->_responseStatus;
    }

    public function withHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }

        return $this;
    }

    public function __get($name)
    {
        return $this->{'_response' . mb_convert_case($name, MB_CASE_TITLE)};
    }
}
