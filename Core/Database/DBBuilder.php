<?php
namespace Core\Database;
use Core\Database\Query;
use Core\Database\Connection;

class DBBuilder {
    
    protected $db = false;
    protected $qry;
    
    public function __construct(){
        Connection::init();
        $this->qry = new Query();
        if(!$this->db){
            $this->db = Connection::getDriver();
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

    public function select($field){
        $this->qry->select($field);
        $this->query($this->qry->getSql());
        return $this;
    }
    public function from($table){
        $this->qry->from($table);
        $this->query($this->qry->getSql());
        return $this;
    }

    public function where($field, $value){
        $p = [$field => $value];
        $this->qry->where($p);
        $this->query($this->qry->getSql());
        return $this;
    }

    public function execute(){
        // $this->db->close();
    }
}
