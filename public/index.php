<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
session_start();
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');

/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', 			['controller' => 'TaskController', 'action' => 'index']);
$router->add('add', 	['controller' => 'TaskController', 'action' => 'create']);
$router->add('edit', 	['controller' => 'TaskController', 'action' => 'edit']);
$router->add('done', 	['controller' => 'TaskController', 'action' => 'done']);

$router->add('login', 		['controller' => 'AdminController', 'action' => 'login']);
$router->add('logout', 		['controller' => 'AdminController', 'action' => 'logout']);


$router->dispatch($_SERVER['QUERY_STRING']);
