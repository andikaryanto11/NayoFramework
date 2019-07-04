<?php

define('DS', DIRECTORY_SEPARATOR, true);
define('BASE_PATH', __DIR__ . DS, TRUE);

$app		= System\App::instance();
$app->request  	= System\Request::instance();
$app->route	= System\Route::instance($app->request);

$route		= $app->route;

/**
 * Your routes go here
 * see Nezamy route for more info at https://nezamy.com/Route/
 * 
 */
$route->get('/', 'App\Controllers\Example@index');
$route->get('example/test', 'App\Controllers\Example@test');

$route->end();
