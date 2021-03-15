<?php

namespace Illuminate\Core;

use Illuminate\Contract\Middleware as MiddlewareInterface;

class Middleware
{
    /**
     * Hold the current active after request middleware callback
     * @var callable $_after
     */
    private $_after;

    /**
     * Hold the current active before request middleware callback
     * @var callable $_before
     */
    private $_before;

    /**
     * Hold the stored middlewares fetched from the source file
     * @var array $_middlewares
     */
    private $_middlewares = [];

    /**
     * Hold the request object 
     * @var \Illuminate\Core\Request $_request
     */
    private $_request;

    /**
     * Hold the response object
     * @var \Illuminate\Core\Response $_response
     */
    private $_response;

    public function __construct(Request $request, Response $response)
    {
        $this->_after = function (Request $request, Response $response) {
        };

        $this->_before = function (Request $request, Response $response) {
        };

        $this->_request = $request;
        $this->_response = $response;

        $this->extractMiddlewares();
    }

    /**
     * Executing before request send middlewares
     * @return mixed
     */
    public function executeBeforeRequest()
    {
        return call_user_func($this->_before, $this->_request, $this->_response);
    }

    /**
     * Executing after request send middlewares
     * @return mixed
     */
    public function executeAfterRequest()
    {
        foreach ($this->resolve('after') as $middleware) {
            $this->registerAfterMiddleware(new $middleware);
        }

        return call_user_func($this->_after, $this->_request, $this->_response);
    }

    /**
     * Registering new before request middleware
     * @param string $middleware
     * @return \Illuminate\Core\Middleware|self
     */
    public function register($middleware)
    {
        return $this->registerBeforeMiddleware(new $middleware);
    }

    /**
     * Registering new before request middleware group
     * @param array $middlewares
     * @return \Illuminate\Core\Middleware|self
     */
    public function registerGroup($middlewares)
    {
        foreach ((array) $middlewares as $middleware) {
            $this->register(new $middleware);
        }

        return $this;
    }

    /**
     * Resolving exact middleware object name
     * @param string $alias
     * @return mixed
     */
    public function resolve($alias)
    {
        return $this->_middlewares[$alias];
    }

    /**
     * Extracting middlewares from the source file
     * @return void
     */
    private function extractMiddlewares()
    {
        $this->_middlewares = require realpath(PATH_APP . 'http/kernel.php');
    }

    /**
     * Registering new middleware
     * @param \Illuminate\Contract\Middleware $middleware
     * @param string $timing
     * @return \Illuminate\Core\Middleware|self
     */
    private function registerMiddleware(MiddlewareInterface $middleware, $timing)
    {
        $nextMiddleware = $this->{$timing};

        $this->{$timing} = function (Request $request, Response $response) use ($middleware, $nextMiddleware) {
            return $middleware->handle($request, $response, $nextMiddleware);
        };

        return $this;
    }

    /**
     * Registering new after request middleware
     * @param \Illuminate\Contract\Middleware $middleware
     * @return \Illuminate\Core\Middleware|self
     */
    private function registerAfterMiddleware(MiddlewareInterface $middleware)
    {
        return $this->registerMiddleware($middleware, '_after');
    }

    /**
     * Registering new before request middleware
     * @param \Illuminate\Contract\Middleware $middleware
     * @return \Illuminate\Core\Middleware|self
     */
    private function registerBeforeMiddleware(MiddlewareInterface $middleware)
    {
        return $this->registerMiddleware($middleware, '_before');
    }
}
