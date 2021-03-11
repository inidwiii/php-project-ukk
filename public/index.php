<?php

/**
 * Including the autoloader file
 * 
 * The autoloader file is included due to its function
 * to autoloading the classes or files automatically
 */

require realpath(__DIR__ . '/../autoload.php');

/**
 * Intantiate the Application class
 * 
 * The Application class is instantiated to initialize
 * the Application to begin executing their program
 */

$app = new \Illuminate\Core\Application;
