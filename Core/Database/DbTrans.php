<?php
namespace Core\Database;
use Core\Database\Connection;

class DbTrans {
    protected static $instance = false;
    public $db = false;
    protected static $driver;

    private function __construct(){

        Connection::init();
        
        if(!$this->db){
            $this->db = Connection::getDriver();
        }
        
    }

    private static function getInstance(){
        if(!self::$instance)    
            self::$instance = new static;
        
        return self::$instance;

    }

    public static function beginTransaction(){
        self::$instance = self::getInstance();
        self::$instance->db->beginTransaction();
        
    }

    public static function rollback(){
        
        self::$instance = self::getInstance();
        self::$instance->db->rollback();
    }

    public static function commit(){
        
        self::$instance = self::getInstance();
        self::$instance->db->commit();
        // self::$instance->db->close();
    }

    public static function getCurrentError(){

        self::$instance = self::getInstance();
        return self::$instance->db->error();
    }

    public static function getCurrentErrorNumber(){
        self::$instance = self::getInstance();
        return self::$instance->db->errno();
    }
}