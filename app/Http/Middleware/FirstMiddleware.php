<?php

namespace App\Http\Middleware;

use Illuminate\Core\Request;
use Illuminate\Core\Response;

class FirstMiddleware extends BaseMiddleware
{
    public function handle(Request $request, Response $response, $next)
    {
        return $next($request, $response);
    }
}
