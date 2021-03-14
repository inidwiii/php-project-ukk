<?php

namespace Illuminate\Facade;

class Route extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Illuminate\Core\Route::class;
    }
}
