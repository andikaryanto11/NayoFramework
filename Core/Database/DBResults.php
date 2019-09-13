<?php
namespace Core\Database;

use Core\Database\Database;
use Core\Database\Connection;

class DBResults {

    protected $sql = "";
    public $db = false;
    protected $connection = false;
    protected $result = array();
    protected $table = "";
    public $fields = array();
    protected $columnOpenMark = "`";
    protected $columnCloseMark = "`";
    

    public function __construct($table = ""){
        $this->table = $table;  

        Connection::init();
        
        $this->driverclass = Connection::getDriverClass();
        if($this->driverclass == "mysqli" || $this->driverclass == "mysql"){
            $this->columnOpenMark = "`";
            $this->columnCloseMark = "`";
        }
        else if($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
            $this->columnOpenMark = "[";
            $this->columnCloseMark = "]";
        }
        
        if(!$this->db){
            $drivertype = !empty(Connection::getDriverType()) ? Connection::getDriverType()."\\" : "Driver\\";
            $ent = "Core\\Database\\".$drivertype.Connection::drivers()[Connection::getDriverClass()];
            // echo $ent;
            $this->db = $ent::getInstance();
            // echo Connection::drivers()[Connection::getDriverClass()];
        }
            
        $this->sql = "select {$this->table}.* from ".$this->table;
        // field collected
        
        if($this->table)
            $this->getFields();
        
    }

    /**
     * @return array array of field name of table
     */
    public function getFields(){
        $this->fields =  $this->db->getFields($this->table);
        return $this->fields;
    }

    /**
     * @return array array of primary key field name of table
     */
    public function pk(){
        return $this->db->pk();
    }

    /**
     * @param string $append string query to append 
     * @return array array object
     */
    public function getAllData(string $append = ""){
        // echo $append;
        $query = $this->db->getAll($this->sql." ".$append); 
        foreach($query as $row) {
            array_push($this->result, $row);
        }
        
        // $this->db->close();

        return $this->result;
    }

    public function getOneData(){

    }

    /**
     * @param int $id id value of table key
     * @return array object result
     */
    public function getById($id){
        
        $this->sql .= " where ".$this->pk()." = ".$id;

        $query = $this->db->getOne($this->sql);

        $this->result = $query;
        
        // $this->db->close();

        return $this->result;
    }

    
    /**
     * @param object $object class object
     * @return int|bool INT id of inserted data, BOOL if fail while insert data
     */

    public function insert($object){
        
            $field_list = array();  //field list string

            $value_list = array();  //value list string

            foreach($object as $key => $value){
                if(isset($value)){
                    $field_list[] = "{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark, false);
                    $value_list[] = "'".escapeString($value)."'";
                }
                    
            }

            $lastid = "";
            if($this->driverclass == 'sqlsrv')
                $lastid = "; SELECT SCOPE_IDENTITY() as last_ins_id;";

            // echo $this->sql;
            $this->sql = "INSERT INTO {$this->table} (".implode(",",$field_list).") VALUES(".implode(",",$value_list).")".$lastid;
            $this->db->query($this->sql);
            if ($this->db->getStatement()) {
                $newid = $this->db->getInsertId();
                // $this->db->close();
                return $newid;
            } else {
                // $this->db->close();
                return false;

            }
    }

    /**
     * @param object $object class object
     * @return int|bool INT id of inserted data, BOOL if fail while insert data
     */
    public function update($object){
        $list = array();
        foreach($object as $key => $value){
            if($key != "Id")
                if(isset($value)){
                        $list[] ="{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark) . " '".escapeString($value)."'";
                } else {
                    $list[] ="{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark) . " NULL";
                }
                
        }
        // $pk = $this->pk();
        $this->sql = "UPDATE {$this->table} SET ".implode(",",$list)." WHERE Id = ".$object->Id;
        $this->db->query($this->sql);
        if ($this->db->getStatement()) {
            // $this->db->close();
            return $object->Id;
        } else {
            // $this->db->close();
            return false;
        }
    }

    
    /**
     * @param int $id id value of table key
     * @return bool TRUE if success, FALSE if fail
     */
    public function delete($id){
        
        $this->sql = "DELETE FROM {$this->table} WHERE Id = ".$id;
        $this->db->query($this->sql);
        $res = $this->db->getStatement();
        // $this->db->close();
        return $res;
    
        
    }

}
