<?php
namespace Core\Libraries;

use Core\Session;

class Redirect {

    private static $instance = false;
    private $url = "";

    private function __construct()
    {
        
    }

    private static function getInstance(){
        if(!self::$instance)
            self::$instance = new self;
        return self::$instance;
    }


    public function with($data){
        $datas = array();
        $session = Session::getInstance();  
        if(is_object($data)) 
            $datas = get_object_vars($data);
        else 
            $datas = $data;
        
        $session->set('data', $datas);
        return self::$instance;
    }

    public static function to($newUrl){
        // echo $newUrl;
        self::getInstance();
        self::$instance->url = $newUrl;
       
        return self::$instance;
    }
    
    public function go(){
        header('Location: '.baseUrl(self::$instance->url));
        exit;
    }

    

}