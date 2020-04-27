<?php
namespace Core\System;

class Config {

    public static function AppAutoload(){
        require APP_PATH. "Config/Autoload.php";
        return $autoload;
    }

    public static function AppConfig(){
        require APP_PATH. "Config/Config.php" ;
        return $config;
    }

    public static function AppDatabase(){
        require APP_PATH . "Config/Database.php";
        return $db;
    }
}