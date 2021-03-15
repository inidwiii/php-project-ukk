<?php

session_start();

$config = json_decode(file_get_contents(realpath(__DIR__ . '/config.json')));

try {
    foreach ($config->autoload->files as $file) {
        $fileAbsPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $file);

        if (!$fileAbsPath) {
            throw new \InvalidArgumentException("Can't find file at '{$file}'.");
        }

        require $fileAbsPath;
    }

    spl_autoload_register(function (string $className) use ($config) {
        $classBase = @explode('\\', $className)[0] . '\\';
        $classPath = $className;

        if (!property_exists($config->autoload->classes, $classBase)) {
            throw new \InvalidArgumentException("Can't find class with name '{$className}'.");
        }

        $classPath = str_replace($classBase, $config->autoload->classes->{$classBase}, $classPath);
        $classPath = strtr($classPath, '\\/', DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR);
        $classPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . $classPath . '.php');

        if (!(bool) $classPath) {
            throw new \InvalidArgumentException("Can't find class with name '{$className}'");
        }

        require $classPath;
    });
} catch (\InvalidArgumentException | \TypeError $exception) {
    die($exception->getMessage());
}
