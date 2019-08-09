<?php

function lang($params = ''){
    
    require CONFIG_PATH . "Config.php";
    
    $languange = $config['language'];
    $param = explode(".", $params);

    require APP_LANGUAGE_PATH.$languange."/".$param[0].".php";

    return $lang[$param[1]];
    
}

function clang($params = ''){

    $param = explode(".", $params);

    require CORE_LANGUAGE_PATH.$param[0].".php";

    return $lang[$param[1]];
}