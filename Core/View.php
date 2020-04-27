<?php
namespace Core;

use eftec\bladeone\BladeOne;

class View {
    private static $blade;

    public static function presentView(string $url = "", $datas = array(), $clearData = true){
        extract($datas) ;
        // $this->session->unset('data');
        include(APP_PATH."Views/".$url.".php");
        if($clearData)
            self::clearData();
    }

    public static function presentBlade($path, $datas = array(), $clearData = true){
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
