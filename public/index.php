<?php

/**
 * Front controller
 */

/**
 * Autoloader
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';

$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
