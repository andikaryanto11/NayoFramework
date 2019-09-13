<?php

use Core\Libraries\Redirect;

function baseUrl($params = ''){
    
    require CONFIG_PATH . "Config.php";

    return $config['base_url'].$params;
    
}

function redirect($newUrl = ""){
    $redirect = Redirect::to($newUrl);
    return $redirect;
}