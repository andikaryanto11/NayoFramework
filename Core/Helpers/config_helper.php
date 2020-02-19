<?php

use Core\System\Config;

function appKey_config(){
    $config = Config::AppConfig();
    return $config['app_key'];
}