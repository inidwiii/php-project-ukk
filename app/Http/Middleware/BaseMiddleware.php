<?php

namespace App\Http\Middleware;

use Illuminate\Contract\Middleware;
use Illuminate\Core\Request;
use Illuminate\Core\Response;

abstract class BaseMiddleware implements Middleware
{
    /**
     * Handle logic for the incoming request
     * @param \Illuminate\Core\Request $request
     * @param \Illuminate\Core\Response $response
     * @param callable $next
     * @return callable
     */
    abstract public function handle(Request $request, Response $response, $next);

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this, $name], $arguments);
    }
}
