<?php

use Core\Libraries\Redirect;
use Core\System\Config;

function baseUrl($params = ''){
    
    // require CONFIG_PATH . "Config.php";
    $config = Config::AppConfig();

    return $config['base_url'].$params;
    
}

function redirect($newUrl = ""){
    $redirect = Redirect::to($newUrl);
    return $redirect;
}