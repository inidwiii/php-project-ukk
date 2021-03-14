<?php

namespace Illuminate\Core;

class Route
{
    const METHOD_GET = 'get';

    const METHOD_POST = 'post';

    private $_current;


    private $_middlewareMap = [];

    private $_namedMap = [];

    private $_request;

    private $_response;

    private $_routeMap = [];

    public function __construct(Request $request, Response $response)
    {
        $this->_request = $request->capture();
        $this->_response = $response;
    }

    public function capture()
    {
        $this->_current = null;

        $requestMethod = $this->_request->method();
        $requestPath = $this->_request->path();

        foreach ($this->_routeMap as $key => $route) {
            if (preg_match($route['reg'], $requestPath, $routeParams) && $route['method'] === $requestMethod) {
                $routeController = $route['callback']['controller'];
                $routeMethod = $route['callback']['method'];
                $routeMiddlewares = $this->_middlewareMap[$key];
                $routeParams = array_slice($routeParams, 1);

                var_dump($this->_middlewareMap);

                if (class_exists($routeController) === false) {
                    throw new \InvalidArgumentException("Can't find controller '{$routeController}'");
                }

                if (method_exists($routeController, $routeMethod) === false) {
                    throw new \InvalidArgumentException("Can't find method '" . get_class($routeController) . "{$routeMethod}'");
                }

                return app()->dispatch($routeController, $routeMethod, ...$routeParams);
            }
        }
    }

    public function get($url, $callback)
    {
        return $this->registerRoute($url, $callback, self::METHOD_GET);
    }

    public function middleware(...$middlewares)
    {
        return $this->registerRouteMiddleware($this->_current, $middlewares);
    }

    public function name($name)
    {
        return $this->registerRouteName($this->_current, $name);
    }

    private function registerRouteMiddleware($url, $middlewares)
    {
        if (is_null($url) || !$url) {
            return $this;
        }

        foreach ($middlewares as $middleware) {
            $this->_middlewareMap[$url][] = $middleware;
        }

        return $this;
    }

    private function registerRouteName($url, $name)
    {
        if (is_null($url) || !$url) {
            return $this;
        }

        $this->_namedMap[$url] = $name;
        return $this;
    }

    private function registerRoute($url, $callback, $method)
    {
        $reg = rtrim($url, '/');
        $reg = preg_replace('/(\/\{\w+\?\})/i', '(?:/([^/]+?))?', $reg);
        $reg = preg_replace('/(\{\w+\})/i', '(?:([^/]+?))', $reg);
        $reg = '/^' . str_replace('/', '\\/', $reg) . '\\/?$/i';

        $this->_current = $url;
        $this->_routeMap[$url] = [
            'url' => $this->_request->base($url),
            'reg' => $reg,
            'callback' => [
                'controller' => $callback[0],
                'method' => $callback[1]
            ],
            'method' => $method
        ];

        return $this->registerRouteMiddleware($url, ['before']);
    }
}
