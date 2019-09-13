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
            $drivertype = !empty(Connection::getDriverType()) ? Connection::getDriverType()."\\" : "Driver\\";
            $ent = "Core\\Database\\".$drivertype.Connection::drivers()[Connection::getDriverClass()];
            // echo $ent;
            $this->db = $ent::getInstance();
            // echo Connection::drivers()[Connection::getDriverClass()];
        }
        
    }

    public static function getInstance(){
        if(!self::$instance)    
            self::$instance = new static;
        
        return self::$instance;

    }

    public static function beginTransaction(){
        self::$instance = self::getInstance();
        self::$instance->db->beginTransaction();
        
    }

    public function rollback(){
        
        self::$instance = self::getInstance();
        self::$instance->db->rollback();
    }

    public function commit(){
        
        self::$instance = self::getInstance();
        self::$instance->db->commit();
        // self::$instance->db->close();
    }
}