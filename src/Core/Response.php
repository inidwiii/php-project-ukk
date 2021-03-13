<?php

namespace Illuminate\Core;

class Response
{
    /**
     * Hold current response headers
     * @var array
     */
    private $_responseHeader;

    /**
     * Hold current response status code
     * @var int
     */
    private $_responseStatus;

    public function __construct()
    {
        $this->_responseHeader = getallheaders();
        $this->_responseStatus = http_response_code();
    }

    /**
     * Get or set HTTP Response headers 
     * @param string $name
     * @param mixed $value
     * @return \Illuminate\Core\Response|string
     */
    public function header($name, $value = null)
    {
        if (is_null($value)) {
            return $this->_responseHeader[$name];
        }

        header("{$name}: {$value}");
        return $this;
    }

    /**
     * Returning HTTP HTML Response
     * @param mixed $response
     * @return \Illuminate\Core\Response
     */
    public function html($response)
    {
        $this->header('Content-Length', strlen($response));
        $this->header('Content-Type', 'text/html');

        echo (string) $response;
        return $this;
    }

    /**
     * Returning HTTP JSON Response
     * @param mixed $response
     * @return \Illuminate\Core\Response
     */
    public function json($response)
    {
        $response = json_encode($response, JSON_PRETTY_PRINT);

        $this->header('Content-Length', strlen($response));
        $this->header('Content-Type', 'application/json');

        echo $response;
        return $this;
    }

    /**
     * Redirecting current page into target url
     * @param string $url
     * @return \Illuminate\Core\Response
     */
    public function redirect($url)
    {
        $this->status(302);
        header("Location: {$url}");
        return $this;
    }

    /**
     * Get or set the current HTTP Response Status Code
     * @param int|null $statusCode
     * @return int
     */
    public function status($statusCode = null)
    {
        if (is_null($statusCode)) {
            return $this->_responseStatus;
        }

        $this->_responseStatus = http_response_code($statusCode);
        return $this->_responseStatus;
    }

    /**
     * Set multiple new response headers data
     * @param array $headers
     * @return \Illuminate\Core\Response
     */
    public function withHeaders($headers)
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
