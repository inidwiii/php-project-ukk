<?php

namespace Illuminate\Core;

class Request
{
    /**
     * Hold host name
     * @var string $_requestHost
     */
    private $_requestHost;

    /**
     * Hold post data
     * @var array $_requestInput
     */
    private $_requestInput;

    /**
     * Hold request method
     * @var string $_requestMethod
     */
    private $_requestMethod;

    /**
     * Hold request path
     * @var string $_requestPath
     */
    private $_requestPath;

    /**
     * Hold port data
     * @var int $_requestPort
     */
    private $_requestPort;

    /**
     * Hold data from query string
     * @var array $_requestQuery
     */
    private $_requestQuery;

    /**
     * Hold request scheme
     * @var string $_requestScheme
     */
    private $_requestScheme;

    private $_requestToken;

    /**
     * Hold request uri
     * @var string $_requestUri
     */
    private $_requestUri;

    public function __construct()
    {
        $this->capture();
    }

    /**
     * Get the base url of the application
     * @param string|null $suffix
     * @return string
     */
    public function base($suffix = null)
    {
        return "{$this->_requestScheme}://{$this->_requestHost}/" . config('app.base') . trim($suffix ?? '/', '/');
    }

    /**
     * Capturing incoming request data and store
     * them into each specific request property
     * @return \Illuminate\Core\Request|self
     */
    public function capture(): \Illuminate\Core\Request
    {
        $this->_requestHost     = $_SERVER['SERVER_NAME'];
        $this->_requestInput    = [];
        $this->_requestMethod   = $_SERVER['REQUEST_METHOD'];
        $this->_requestPath     = parse_url($_SERVER['REQUEST_URI'])['path'];
        $this->_requestPort     = $_SERVER['SERVER_PORT'];
        $this->_requestQuery    = [];
        $this->_requestScheme   = $_SERVER['REQUEST_SCHEME'];
        $this->_requestToken    = $_SESSION['_token'];
        $this->_requestUri      = str_replace(PATH_BASE, '', $this->_requestPath);

        foreach ($_GET as $key => $v) {
            $this->_requestQuery[$key] = filter_input(INPUT_GET, $key);
        }

        foreach ($_POST as $key => $v) {
            $this->_requestInput[$key] = filter_input(INPUT_POST, $key);
        }

        return $this;
    }

    /**
     * Get the full requested URL with the query string
     * @return string
     */
    public function fullUrl()
    {
        return $this->url(true);
    }

    /**
     * Get one or all data from the post data
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function input($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->_requestInput;
        }

        if (!(bool) array_key_exists($key, $this->_requestInput)) {
            return value($default);
        }

        return $this->_requestInput[$key];
    }

    /**
     * Get the current requested method
     * @return string
     */
    public function method()
    {
        return mb_convert_case($this->_requestMethod, MB_CASE_LOWER);
    }

    /**
     * Get the current request path, with base trimming 
     * @return string
     */
    public function path()
    {
        return str_replace(config('app.base'), '', $this->_requestPath);
    }

    /**
     * Get one or all data from the query string
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function query($key = null, $default = null)
    {
        if (is_null($key)) {
            return $this->_requestQuery;
        }

        if (!(bool) array_key_exists($key, $this->_requestQuery)) {
            return value($default);
        }

        return $this->_requestQuery[$key];
    }

    /**
     * Get the requested URL with or without query string
     * @param bool $full
     * @return string
     */
    public function url($full = false)
    {
        $url  = "{$this->_requestScheme}://{$this->_requestHost}{$this->_requestPath}";
        $url .= $full && !empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '';

        return $url;
    }

    /**
     * Handle the logic to validating the CSRF Token Given
     * @return bool
     */
    public function validateCSRFToken()
    {
        $_token = $this->input('_token');

        if (is_null($_token) || !(bool) $_token) {
            return false;
        }

        return (bool) $_token === $this->_requestToken;
    }

    public function __get($name)
    {
        return $this->{'_request' . mb_convert_case($name, MB_CASE_TITLE)};
    }
}
