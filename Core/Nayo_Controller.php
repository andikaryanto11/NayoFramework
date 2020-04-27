<?php
namespace Core;
use Core\Request;
use Core\CSRF;
use eftec\bladeone\BladeOne;

class Nayo_Controller{
    protected $request = false;
    // protected $session = false;
    protected $blade = false;
    // protected $security = false;
    public $tokenname = "";
    public $tokenhash = "";

    public function __construct(){

        if(!$this->request)
            $this->request = Request::getInstance();
        
        // if(!$this->session)
        //     $this->session = Session::getInstance();

        if($GLOBALS['config']['csrf_security']){
            switch($this->request->request()['REQUEST_METHOD']){
                case 'POST' :
                    if(!hash_equals(Session::get('csrfToken'), $this->request->post(CSRF::getCsrfTokenName()))){
                        die('Invalid Token');
                    }
                    Session::set('csrfName', CSRF::getCsrfTokenName());
                    Session::set('csrfToken', CSRF::getCsrfHash());
                    break;
                default : 
                    Session::set('csrfName', CSRF::getCsrfTokenName());
                    Session::set('csrfToken', CSRF::getCsrfHash());
                    break;
            }
        } else {
            if(Session::get('csrfName')){
                Session::unset('csrfName');
                Session::unset('csrfToken');
                
            }
        }
    }

    public function view(string $url = "", $datas = array(), $asText = false, $clearData = true){
        extract($datas) ;
        if($asText){
            ob_start();
            include(APP_PATH."Views/".$url.".php");
            $return = ob_get_clean();
            return $return;
        }
            
        include(APP_PATH."Views/".$url.".php");
        if($clearData)
            $this->clearData();

    }

    private function clearData(){
        Session::unset('data');
    }

    public function blade($path, $datas = array(), $asText = false, $clearData = true){
        if($asText){
            extract($datas) ;
            ob_start();
            include(APP_PATH."Views/".str_replace(".", "/", $path).".php");
            $return = ob_get_clean();
            return $return;
        }
        $this->blade = new BladeOne(APP_PATH."Views/", APP_CACHE, BladeOne::MODE_AUTO);
        $this->bladeInclude();
        echo $this->blade->run($path, $datas);
        if($clearData)
            $this->clearData();

    }

    private function bladeInclude(){
        
        $this->blade->addInclude("includes.input", 'input');
        $this->blade->addInclude("includes.label", 'label');
    }

    public function input(string $var){
        return $_POST[$var];
    }

    

}
