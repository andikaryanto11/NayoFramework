<?php

function baseUrl($params = ''){
    
    require CONFIG_PATH . "Config.php";

    return $config['base_url'].$params;
    
}

function redirect($newUrl = ""){
    header('Location: '.baseUrl($newUrl));
}