<?php

namespace Illuminate\Core;

use Illuminate\Contract\Middleware as MiddlewareInterface;

class Middleware
{
    private $_after;

    private $_before;

    private $_middlewares = [];

    private $_request;

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

    public function executeBeforeRequest()
    {
        return call_user_func($this->_before, $this->_request, $this->_response);
    }

    public function executeAfterRequest()
    {
        foreach ($this->resolve('after') as $middleware) {
            $this->registerAfterMiddleware(new $middleware);
        }

        return call_user_func($this->_after, $this->_request, $this->_response);
    }

    public function register($middleware)
    {
        return $this->registerBeforeMiddleware(new $middleware);
    }

    public function registerGroup($middlewares)
    {
        foreach ((array) $middlewares as $middleware) {
            $this->register(new $middleware);
        }
    }

    public function resolve($alias)
    {
        return $this->_middlewares[$alias];
    }

    private function extractMiddlewares()
    {
        $this->_middlewares = require realpath(PATH_APP . 'http/kernel.php');
    }

    private function registerMiddleware(MiddlewareInterface $middleware, $timing)
    {
        $nextMiddleware = $this->{$timing};

        $this->{$timing} = function (Request $request, Response $response) use ($middleware, $nextMiddleware) {
            return $middleware->handle($request, $response, $nextMiddleware);
        };

        return $this;
    }

    private function registerAfterMiddleware(MiddlewareInterface $middleware)
    {
        return $this->registerMiddleware($middleware, '_after');
    }

    private function registerBeforeMiddleware(MiddlewareInterface $middleware)
    {
        return $this->registerMiddleware($middleware, '_before');
    }
}
