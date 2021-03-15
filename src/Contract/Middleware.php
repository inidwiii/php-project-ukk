<?php

namespace Illuminate\Contract;

use Illuminate\Core\Request;
use Illuminate\Core\Response;

interface Middleware
{
    /**
     * Handle logic for the incoming request
     * @param \Illuminate\Core\Request $request
     * @param \Illuminate\Core\Response $response
     * @param callable $next
     * @return callable
     */
    public function handle(Request $request, Response $response, $next);
}
