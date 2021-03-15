<?php

namespace Illuminate\Core;

class Route
{
    const METHOD_GET = 'get';

    const METHOD_POST = 'post';

    /**
     * Hold the current registered route
     * @var null|string $_current
     */
    private $_current;

    private $_middleware;

    /**
     * Hold the middlewares attached to the route
     * @var array $_middlewareMap
     */
    private $_middlewareMap = [];

    /**
     * Hold the alias name of the routes
     * @var array $_namedMap
     */
    private $_namedMap = [];

    /**
     * Hold the request object instance
     * @var \Illuminate\Core\Request $_request
     */
    private $_request;

    /**
     * Hold the response object instance
     * @var \Illuminate\Core\Response $_response
     */
    private $_response;

    /**
     * Hold all the routes registered
     * @var array $_routeMap
     */
    private $_routeMap = [];

    public function __construct(Middleware $middleware, Request $request, Response $response)
    {
        $this->_middleware = $middleware;
        $this->_request = $request;
        $this->_response = $response;
    }

    /**
     * Capture and registering route into routeMap
     * @return mixed|void
     */
    public function capture()
    {
        $this->_current = null;
        $this->extractRoute();

        $requestMethod = $this->_request->method();
        $requestPath = $this->_request->path();

        foreach ($this->_routeMap as $routeId => $route) {
            if (preg_match($route['reg'], $requestPath, $routeParams) && $route['method'] === $requestMethod) {
                extract($this->extractRouteData($routeId, $route, $routeParams));

                foreach ($this->_middlewareMap[$routeId] as $middleware) {
                    $middleware = $this->_middleware->resolve($middleware);

                    if (is_array($middleware)) {
                        $this->_middleware->registerGroup($middleware);
                        continue;
                    }

                    $this->_middleware->register(new $middleware);
                }

                $this->_middleware->executeBeforeRequest();

                // current
                $responseContent = app()->dispatch($routeController, $routeMethod, ...$routeParams);

                $this->_response->withHeaders([
                    'X-App-Name'        => config('app.name'),
                    'X-App-Lang'        => config('app.lang'),
                    'Content-Length'    => strlen($responseContent)
                ])->status(200);

                echo $responseContent;
                // end current

                $this->_middleware->executeAfterRequest();

                return;
            }
        }

        $this->_response->status(404);
        die('404 - Not Found');
    }

    /**
     * Registering new route with method 'GET'
     * @param string $url
     * @param array $callback
     * @return \Illuminate\Core\Route|self
     */
    public function get($url, $callback)
    {
        return $this->registerRoute($url, $callback, self::METHOD_GET);
    }

    /**
     * Registering new route with method 'POST'
     * @param string $url
     * @param array $callback
     * @return \Illuminate\Core\Route|self
     */
    public function post($url, $callback)
    {
        return $this->registerRoute($url, $callback, self::METHOD_POST);
    }

    /**
     * Registering middlewares toward the current route
     * @param string[] $middlewares
     * @return \Illuminate\Core\Route|self
     */
    public function middleware(...$middlewares)
    {
        return $this->registerRouteMiddleware($this->_current, $middlewares);
    }

    /**
     * Registering an alias name toward the current route
     * @param string $name
     * @return \Illuminate\Core\Route|self
     */
    public function name($name)
    {
        return $this->registerRouteName($this->_current, $name);
    }

    /**
     * Collecting all route that need to be registered
     * @return void
     */
    private function extractRoute()
    {
        foreach (glob(PATH_ROUTE . '*.php') as $routeFile) {
            require $routeFile;
        }
    }

    /**
     * Collect and extracting required data from current matched route
     * @param string $routeId
     * @param array $routeData
     * @param array $routeParams
     * @return array
     */
    private function extractRouteData($routeId, $routeData, $routeParams)
    {
        return [
            'routeController' => $routeData['callback']['controller'],
            'routeMethod' => $routeData['callback']['method'],
            'routeMiddleware' => $this->_middlewareMap[$routeId],
            'routeParams' => array_slice($routeParams, 1)
        ];
    }

    /**
     * Extracting or creating route regex from the given url
     * @param string $url
     * @return string
     */
    private function extractRouteRegex($url)
    {
        $reg = rtrim($url, '/');
        $reg = preg_replace('/(\/\{\w+\?\})/i', '(?:/([^/]+?))?', $reg);
        $reg = preg_replace('/(\{\w+\})/i', '(?:([^/]+?))', $reg);
        $reg = '/^' . str_replace('/', '\\/', $reg) . '\\/?$/';

        return $reg;
    }

    /**
     * Registering new middleware
     * @param string $url
     * @param array $middlewares
     * @return \Illuminate\Core\Route|self
     */
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

    /**
     * Registering alias name
     * @param string $url
     * @param string $name
     * @return \Illuminate\Core\Route|self
     */
    private function registerRouteName($url, $name)
    {
        if (is_null($url) || !$url) {
            return $this;
        }

        $this->_namedMap[$url] = $name;
        return $this;
    }

    /**
     * Registering nw route
     * @param string $url
     * @param string $callback
     * @param string $method
     * @return \Illuminate\Core\Route|self
     */
    private function registerRoute($url, $callback, $method)
    {

        $this->_current = $url;
        $this->_routeMap[$url] = [
            'url' => $this->_request->base($url),
            'reg' => $this->extractRouteRegex($url),
            'callback' => [
                'controller' => $callback[0],
                'method' => $callback[1]
            ],
            'method' => $method
        ];

        return $this->registerRouteMiddleware($url, ['before']);
    }
}
