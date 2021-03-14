<?php

namespace Illuminate\Facade;

class Request extends Facade
{
    /**
     * Get the accessor to the exact main class
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Illuminate\Core\Request::class;
    }
}
