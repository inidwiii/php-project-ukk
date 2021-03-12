<?php

namespace Illuminate\Core;

class Config
{
    /**
     * Hold the configurations data
     * @var array $_repository
     */
    private $_repository;

    public function __construct()
    {
        $this->capture();
    }

    /**
     * Get data or value from the configurations repository
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return array_get($this->_repository, $key, $default);
    }

    /**
     * Insert data or value into the configuration repository
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function set(string $key, $value)
    {
        return array_set($this->_repository, $key, $value);
    }

    /**
     * Capturing data that are stored as a configuration in
     * a configuration file under the configuration folder
     * @return void
     * @throws \InvalidArgumentException
     */
    private function capture()
    {
        $root = PATH_CONFIG;

        if ($root === DS) {
            throw new \InvalidArgumentException("Config folder is not found.");
        }

        foreach (glob($root . '*.php') as $configFilepath) {
            $configFilename = preg_replace('/(\.(php))$/', '', basename($configFilepath));
            $this->_repository[$configFilename] = (array) require $configFilepath;
        }
    }
}
