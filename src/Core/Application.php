<?php

namespace Illuminate\Core;

class Application
{
    /**
     * @var \Illuminate\Core\Application $_instance
     */
    private static $_instance;

    public function __construct()
    {
        self::$_instance = $this;

        define('DS', DIRECTORY_SEPARATOR);
        define('ROOT', realpath(dirname(__DIR__, 2)) . DS);
        define('PATH_BASE', '/ukk');
        define('PATH_APP', realpath(ROOT . 'app') . DS);
        define('PATH_CONFIG', realpath(ROOT . 'config') . DS);
        define('PATH_LIB', realpath(ROOT . 'src') . DS);
        define('PATH_PUBLIC', realpath(ROOT . 'public') . DS);
        define('PATH_ROUTE', realpath(ROOT . 'routes') . DS);
    }

    /**
     * Get the runtime of the Application
     * @return float
     */
    public function runtime(): float
    {
        return round(microtime(true) - START_TIME, 4);
    }

    /**
     * Get the instance of the Application class
     * @return \Illuminate\Core\Application|self
     */
    public static function instance(): \Illuminate\Core\Application
    {
        return self::$_instance;
    }
}
