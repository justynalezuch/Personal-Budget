<?php

/**
 * Front controller
 */

/**
 * Autoloader
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */
session_start();

$router = new Core\Router();

// Add the routes
$router->add('financial-balance/{period:[a-z0-9-]+}', ['controller' => 'FinancialBalanceReview', 'action' => 'index']);
$router->add('financial-balance', ['controller' => 'FinancialBalanceReview', 'action' => 'index']);

$router->add('settings', ['controller' => 'Settings', 'action' => 'index']);
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('login', ['controller' => 'Login', 'action' => 'new']);


$router->dispatch($_SERVER['QUERY_STRING']);
