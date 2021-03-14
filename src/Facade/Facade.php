<?php

namespace Illuminate\Facade;

abstract class Facade
{
    /**
     * Get the accessor to the exact main class
     * @return string
     */
    abstract public static function getFacadeAccessor();

    /**
     * Handle on call to method
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return app()->call(static::getFacadeAccessor(), $name, ...$arguments);
    }

    /**
     * Handle on property request
     * @param string $name 
     * @return mixed
     */
    public function __get($name)
    {
        return app(static::getFacadeAccessor())->{$name};
    }

    /**
     * Handle on replace or assigning new property
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        app(static::getFacadeAccessor())->{$name} = $value;
    }

    /**
     * Handle on call method statically 
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return app()->call(static::getFacadeAccessor(), $name, ...$arguments);
    }
}
