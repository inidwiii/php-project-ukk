<?php

return [
    'after' => [],
    'before' => [
        \App\Http\Middleware\CSRFVerifyMiddleware::class
    ],

    'first' => \App\Http\Middleware\FirstMiddleware::class,
    'second' => \App\Http\Middleware\SecondMiddleware::class
];
