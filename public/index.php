<?php

/**
 * EGovernmenet
 * 
 * A simple application made for the government
 * to handle public complaints toward them
 * 
 * @author inidwiii  <ini.dwiii@gmail.com>
 * @copyright 2021 EGovernment
 * @package App
 * @package Illuminate
 */

define('START_TIME', microtime(true));

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
$req = new \Illuminate\Core\Request;
$res = new \Illuminate\Core\Response;

if ($req->url() === 'http://localhost/ukk/') $res->redirect('http://localhost/ukk/redirect');
