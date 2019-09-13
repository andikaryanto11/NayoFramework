<?php
namespace Core;

class Session {

    private static $instance = false;

    private function __construct(){
        
    }

    public static function getInstance(){
        if(!self::$instance)
            self::$instance = new self;
        
        return self::$instance;
    }
    /**
     * @param string $name 
     * @param string $value 
     */

    public function set($name, $value){
        $_SESSION[$name] = $value;
    }

    /**
     * @param string $name 
     * @param string $value 
     */
    public function get($name){
        if(isset($_SESSION[$name]))
            return $_SESSION[$name];
        return null;
    }

    /** 
     */
    public function destroy(){
        session_destroy();
    }

    /**
     * @param string $name 
     */
    public function unset($name){
        unset($_SESSION[$name]);
    }

    /**
     * @param string $name 
     * @param array $value 
     */
    public function setFlash($name, $value = array()){
        if (!isset($_SESSION['flash'])) {
            $_SESSION['flash'] = array();
        }
        $_SESSION['flash'][$name] = $value;
    }

    /**
     * @param string $name 
     */
    public function getFlash($name){
        if (isset($_SESSION['flash'][$name])) {
            $flash = $_SESSION['flash'][$name];
            unset($_SESSION['flash'][$name]);
            return $flash;
        }
        return array();
    }

    /**
     * @param string $name 
     */
    public function isFlashExist($name){
        if (isset($_SESSION['flash'][$name])) {
           
            return true;
        }
        return false;
    }
}