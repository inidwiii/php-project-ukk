<?php

namespace App\Http\Controller;

use Illuminate\Core\Route;

abstract class BaseController
{
    protected $_route;

    protected function __construct(Route $route)
    {
        $this->_route = $route;
    }

    protected function middleware(...$middlewares)
    {
        $this->_route->middleware(...$middlewares);
        return $this;
    }
}
