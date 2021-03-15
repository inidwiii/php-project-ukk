<?php

namespace App\Http\Middleware;

use Illuminate\Core\Request;
use Illuminate\Core\Response;

class FirstMiddleware extends BaseMiddleware
{
    /**
     * Handle logic for the incoming request
     * @param \Illuminate\Core\Request $request
     * @param \Illuminate\Core\Response $response
     * @param callable $next
     * @return callable
     */
    public function handle(Request $request, Response $response, $next)
    {
        echo 'first';
        return $next($request, $response);
    }
}
