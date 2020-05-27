<?php
namespace Core\Database;

use Core\Database\Database;
use Core\Database\Connection;
use Core\Database\Query;

class DBResults {

    protected $sql = "";
    public $db = false;
    protected $connection = false;
    protected $result = array();
    protected $table = "";
    public $fields = array();
    protected $columnOpenMark = "`";
    protected $columnCloseMark = "`";
    protected $filter = [];
    

    public function __construct($table = "", $filter = []){
        $this->table = $table;  
        $this->filter = $filter;

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
            $this->db = Connection::getDriver();
        }
        
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
    public function getAllData(){
        $query = new Query();
        $querysql = $query->selectFromtable($this->table, $this->filter);
        $query = $this->db->getAll($querysql); 
        foreach($query as $row) {
            array_push($this->result, $row);
        }
        
        return $this->result;
    }

    public function getOneData(){

    }

    /**
     * @param int $id id value of table key
     * @return array object result
     */
    public function getById($id){


        $query = new Query();
        
        $p = [
            'where' => [
                'Id' => $id
            ]
        ];

        $sql = $query->selectFromtable($this->table, $p);
        
        $query = $this->db->getOne($sql);

        $this->result = $query;

        return $this->result;
    }

    
    /**
     * @param object $object class object
     * @return int|bool INT id of inserted data, BOOL if fail while insert data
     */

    public function insert($object){
        
        $query = new Query();
        $sql = $query->insertInto($this->table, $object);
        $this->db->insert($sql);
        if ($this->db->getStatement()) {
            $newid = $this->db->getInsertId();
            return $newid;
        } else {
            return false;

        }
    }

    /**
     * @param object $object class object
     * @return int|bool INT id of inserted data, BOOL if fail while insert data
     */
    public function update($object){
        
        $query = new Query();
        $sql = $query->update($this->table, $object);
        $this->db->query($sql);
        if ($this->db->getStatement()) {
            return $object->Id;
        } else {
            return false;
        }
    }

    
    /**
     * @param int $id id value of table key
     * @return bool TRUE if success, FALSE if fail
     */
    public function delete($id){
        
        $query = new Query();
        $sql = $query->delete($this->table, $id);
        $this->db->query($sql);
        $res = $this->db->getStatement();
        return $res;
        
    }

    public function count($filter = []){

        $query = new Query();
        $sql = $query->selectCount($this->table, $this->filter);
        $query = $this->db->getOne($sql); 
        return $query['Counted'];
    }

}
