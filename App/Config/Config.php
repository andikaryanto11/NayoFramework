<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * set for you base_url
 * ex : http://localhost/YourDirectory/
 */
$config['base_url'] = '';


/**
 * default language for application resource
 */
$config['language'] = 'en';

/**  
 * Migration 
 * 
 * if $config['enable_auto_migration'] set True Then you can use Nayo_migration function
 * 
*/
$config['enable_auto_migration'] = TRUE;

/**  
 * Migration 
 * 
 * if $config['enable_auto_seeds'] set True Then you can use Nayo_migration function
 * 
*/
$config['enable_auto_seed'] = TRUE;


/**
 * set true if Use CSRF 
 */
$config['csrf_security'] = TRUE;
