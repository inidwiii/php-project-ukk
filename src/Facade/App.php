<?php

namespace Illuminate\Facade;

class App extends Facade
{
    /**
     * Get the accessor to the exact main class
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return \Illuminate\Core\Application::class;
    }
}
