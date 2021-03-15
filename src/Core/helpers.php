<?php

use Illuminate\Core\Application;
use Illuminate\Core\Config;
use Illuminate\Core\Request;

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
            return $appInstance->make($abstract);
        }

        return $appInstance;
    }
}

if (!function_exists('array_get')) {
    /**
     * Get data or value from the array with dot notation
     * @param array $array
     * @param string|int $key
     * @param mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (!is_array($array)) {
            return value($default);
        }

        if (is_null($key)) {
            return $array;
        }

        if ((bool) array_key_exists($key, $array)) {
            return $array[$key];
        }

        $keys = explode('.', $key);
        $temp = $array;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            if (!(bool) array_key_exists($key, $temp)) {
                return value($default);
            }

            if (is_array($temp[$key])) {
                $temp = $temp[$key];
            }
        }

        return $temp[array_shift($keys)];
    }
}

if (!function_exists('array_set')) {
    /**
     * Insert value or data into an array with dot notation
     * @param array &$array
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    function array_set(&$array, $key, $value = null)
    {
        if (!is_array($array)) {
            return;
        }

        if (is_null($key)) {
            return $array = $value;
        }

        $keys = explode('.', $key);
        $temp = &$array;

        while (count($keys) > 1) {
            $key = array_shift($keys);

            // if the current value is not set and is not an array, we
            // will assign them as an empty array and keep digging
            if (!isset($temp[$key]) || !is_array($temp[$key])) {
                $temp[$key] = [];
            }

            $temp = &$temp[$key];
        }

        $temp[array_shift($keys)] = $value;
        return $value;
    }
}

if (!function_exists('config')) {
    /**
     * Get the config data stored globally
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config($key, $default = null)
    {
        return app(Config::class)->get($key, $default);
    }
}

if (!function_exists('csrf')) {
    /**
     * Creating input template / blueprint for including CSRF token
     * @return string
     */
    function csrf()
    {
        $_token = $_SESSION['_token'];
        return '<input type="hidden" name="_token" value="' . $_token . '" />';
    }
}

if (!function_exists('value')) {
    /**
     * Get the exact value with proper execution
     * @param mixed $var
     * @return mixed
     */
    function value($var)
    {
        if (is_callable($var)) {
            return call_user_func($var);
        }

        return $var;
    }
}
