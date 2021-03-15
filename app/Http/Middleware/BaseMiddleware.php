<?php

namespace App\Http\Middleware;

use Illuminate\Contract\Middleware;
use Illuminate\Core\Request;
use Illuminate\Core\Response;

abstract class BaseMiddleware implements Middleware
{
    abstract public function handle(Request $request, Response $response, $next);

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this, $name], $arguments);
    }
}
