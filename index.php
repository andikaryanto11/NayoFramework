<?php

/**
 * requires and define path
 */
require_once('core/Nayo.php');

define("DS", DIRECTORY_SEPARATOR);

define("ROOT", getcwd() . DS);

define("BASE_PATH", ROOT);

require BASE_PATH.'vendor/autoload.php';

/**
 * check if app runs via CLI
 */
$args = array();
if(php_sapi_name() === 'cli'){
    $args = $argv;  
}
    

/**
 * Run main apps
 */
Nayo::run($args);



