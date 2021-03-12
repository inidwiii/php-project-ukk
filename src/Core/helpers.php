<?php

use Illuminate\Core\Application;

if (!function_exists('app')) {
    /**
     * Get the static instance of the Application class or the 
     * instance that stored as a globally shared instance
     * @param string|null $abstract
     * @return \Illuminate\Core\Application|object
     * @throws \InvalidArgumentException
     */
    function app($abstract = null)
    {
        $appInstance = Application::instance();

        if (!is_null($abstract)) {
            return $appInstance;
        }

        return $appInstance;
    }
}
