<?php
namespace Core;

use eftec\bladeone\BladeOne;

class View {
    private static $blade;

    public static function presentView(string $url = "", $datas = array(), $astext = false, $clearData = true){
        extract($datas) ;
        if($asText){
            ob_start();
            include(APP_PATH."Views/".$url.".php");
            $return = ob_get_clean();
            return $return;
        }
        include(APP_PATH."Views/".$url.".php");
        if($clearData)
            self::clearData();
    }

    public static function presentBlade($path, $datas = array(), $asText= false, $clearData = true){
        if($asText){
            extract($datas) ;
            ob_start();
            include(APP_PATH."Views/".str_replace(".", "/", $path).".php");
            $return = ob_get_clean();
            return $data;
        }

        self::$blade = new BladeOne(APP_PATH."Views/", APP_CACHE, BladeOne::MODE_AUTO);
        self::bladeInclude();
        echo self::$blade->run($path, $datas);
        if($clearData)
            self::clearData();
    }

    private static function clearData(){
        $session = Session::getInstance();
        $session->unset('data');
    }

    private static function bladeInclude(){
        
        self::$blade->addInclude("includes.input", 'input');
        self::$blade->addInclude("includes.label", 'label');
    }

    
}
