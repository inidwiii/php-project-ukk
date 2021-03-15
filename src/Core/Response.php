<?php

namespace Illuminate\Core;

use Illuminate\Facade\Redirect;

class Response
{
    /**
     * Hold current response content
     * @var string
     */
    private $_responseContent;

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
     * Aborting the route to the spesific error code
     * @param int $statusCode
     * @return \Illuminate\Core\Response|self
     */
    public function abort($statusCode)
    {
        $this->status($statusCode);
        Redirect::abort($statusCode);
        return $this;
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

        $this->_responseHeader[$name] = $value;
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
        $this->_responseContent = $response;
        $this->header('Content-Type', 'text/html');

        return $this;
    }

    /**
     * Returning HTTP JSON Response
     * @param mixed $response
     * @return \Illuminate\Core\Response
     */
    public function json($response)
    {
        $this->_responseContent = json_encode($response, JSON_PRETTY_PRINT);
        $this->header('Content-Type', 'application/json');

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

    public function __toString()
    {
        return $this->_responseContent;
    }
}
