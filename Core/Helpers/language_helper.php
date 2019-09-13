<?php

function lang($params = ''){
    
    require CONFIG_PATH . "Config.php";
    
    $language = $config['language'];
    
    if(isset(app()->language))
        $language = app()->language;

    $param = explode(".", $params);

    require APP_LANGUAGE_PATH.$language."/".$param[0].".php";

    return $lang[$param[1]];
    
}

function clang($params = ''){

    $param = explode(".", $params);

    require CORE_LANGUAGE_PATH.$param[0].".php";

    return $lang[$param[1]];
}