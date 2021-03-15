<?php

namespace Illuminate\Core;

class Application extends Container
{
    /**
     * @var \Illuminate\Core\Application $_instance
     */
    private static $_instance;

    public function __construct()
    {
        self::$_instance = $this;

        defined('DS') or define('DS', DIRECTORY_SEPARATOR);
        defined('ROOT') or define('ROOT', realpath(dirname(__DIR__, 2)) . DS);
        defined('PATH_BASE') or define('PATH_BASE', '/ukk');
        defined('PATH_APP') or define('PATH_APP', realpath(ROOT . 'app') . DS);
        defined('PATH_CONFIG') or define('PATH_CONFIG', realpath(ROOT . 'config') . DS);
        defined('PATH_LIB') or define('PATH_LIB', realpath(ROOT . 'src') . DS);
        defined('PATH_PUBLIC') or define('PATH_PUBLIC', realpath(ROOT . 'public') . DS);
        defined('PATH_ROUTE') or define('PATH_ROUTE', realpath(ROOT . 'routes') . DS);

        $this->singleton(Application::class);
        $this->singleton(Config::class);
        $this->singleton(Request::class);
        $this->singleton(Response::class);
        $this->singleton(Middleware::class);
        $this->singleton(Route::class);
    }

    public function initialize()
    {
        $this->createCSRFToken();
        $this->call(Route::class, 'capture');
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

    private function createCSRFToken()
    {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
    }
}
