<?php

/**
 * requires and define path
 */
require_once('Core/Nayo.php');
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



