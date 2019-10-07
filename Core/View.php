<?php
namespace Core;

class View {

    public static function present(string $url = "", $datas = array(), $clearData = true){
        extract($datas) ;
        // $this->session->unset('data');
        include(APP_PATH."Views/".$url.".php");
        if($clearData)
            self::clearData();
    }

    private static function clearData(){
        $session = Session::getInstance();
        $session->unset('data');
    }

    
}
