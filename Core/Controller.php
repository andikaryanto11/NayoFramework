<?php
namespace Core;
use Core\Request;
use Core\CSRF;
Use eftec\bladeone\BladeOne;

class Nayo_Controller{
    protected $request = false;
    protected $session = false;
    protected $blade = false;
    // protected $security = false;
    public $tokenname = "";
    public $tokenhash = "";

    public function __construct(){

        if(!$this->request)
            $this->request = new Request();
        if(!$this->session)
            $this->session = new Session();

        if($GLOBALS['config']['csrf_security']){
            switch($this->request->request()['REQUEST_METHOD']){
                case 'POST' :
                    if(!hash_equals($this->session->get('csrfToken'), $this->request->post(CSRF::getCsrfTokenName()))){
                        die('Invalid Token');
                    }
                    $this->session->set('csrfName', CSRF::getCsrfTokenName());
                    $this->session->set('csrfToken', CSRF::getCsrfHash());
                    break;
                default : 
                    $this->session->set('csrfName', CSRF::getCsrfTokenName());
                    $this->session->set('csrfToken', CSRF::getCsrfHash());
                    break;
            }
        } else {
            if($this->session->get('csrfName')){
                $this->session->unset('csrfName');
                $this->session->unset('csrfToken');
                
            }
        }
    }

    public function view(string $url = "", $datas = array()){
        // echo $url;
        extract($datas) ;
        include(APP_PATH."Views/".$url.".php");

    }

    public function blade($path, $datas = array()){

        $this->blade = new BladeOne(APP_PATH."Views/", APP_CACHE, BladeOne::MODE_AUTO);
        $this->bladeInclude();
        echo $this->blade->run($path, $datas);

    }

    private function bladeInclude(){
        
        $this->blade->addInclude("includes.input", 'input');
        $this->blade->addInclude("includes.label", 'label');
    }

    public function input(string $var){
        return $_POST[$var];
    }

    

}