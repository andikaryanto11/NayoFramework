<?php
namespace Core;

class Request {

    private static $instance = false;

    private function __construct(){
        
    }

    public static function getInstance(){
        if(!self::$instance)
            self::$instance = new self;
        
        return self::$instance;
    }
    /**
     * @param string $var 
     */
    public function post(string $var){
        if(isset($_POST[$var]))
            return $_POST[$var];
        return null;
    }

    /**
     * @param string $var 
     */
    public function get(string $var){
        if(isset($_GET[$var]))
            return $_GET[$var];
        return null;
    }

    /**
     * 
     */
    public function body(){
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
       } else {
            return $_GET;
       }
    }

    public function files($name){
        if(isset($_FILES[$name]))
            return $_FILES[$name];
        return null;
    }

    public function type(){
        return $_SERVER['REQUEST_METHOD'];
    }

    public function request(){
        return $_SERVER;
    }
}