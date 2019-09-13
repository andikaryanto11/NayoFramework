<?php
namespace Core\Database;
use Core\Database\Connection;

class DBBuilder {
    
    protected $db = false;
    
    
    public function __construct(){
        Connection::init();

        if(!$this->db){
            $drivertype = !empty(Connection::getDriverType()) ? Connection::getDriverType()."\\" : "Driver\\";
            $ent = "Core\\Database\\".$drivertype.Connection::drivers()[Connection::getDriverClass()];
            // echo $ent;
            $this->db = $ent::getInstance();
            // echo Connection::drivers()[Connection::getDriverClass()];
        }
    }

    /**
     * @param string $sql string of sql query
     * @return array array object of query
     */
    public function query(string $sql){
        $this->db->query($sql);
        return $this;
    }

    
    public function multiQuery(string $sql){
        $this->db->multiQuery($sql);
        return $this;
    }

    
    public function fetchObject(){
        $result = $this->db->fetchObject();
        // $this->db->close();
        return $result;
    }

    public function fetch(){
        $result = $this->db->fetch();
        // $this->db->close();
        return $result;
    }

    public function execute(){
        // $this->db->close();
    }
}
