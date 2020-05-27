<?php

namespace Core;

use Core\Database\DBResults;
use Core\Libraries\Clslist;
use Core\System\Config;

class Nayo_Model
{
    protected $db = false;
    protected $db_result = false;
    private $type = "string";

    public function __construct()
    {
    }

    // public function __set($name, $value) {
    //     if (!isset($this->$$name)) {
    //         throw new Exception($name.' property does not exist');
    //     }
    // }

    // public function __get($name) {
    //     if (!isset($this->$$name)) {
    //         $ex = new Exception('Undefined variable: $'.$name);
    //         Nayo_Exception::exceptionHandler($ex);
    //     }
    // }
    public static function countAll($filter = array())
    {
        $instance = new static;
        return $instance->count($filter);
    }

    public function count($filter = array())
    {
        $db_result = new DBResults($this->table, $filter);
        return $db_result->count();
    }

    public static function getAll($filter = array())
    {
        $instance = new static;
        return $instance->findAll($filter);
    }

    public static function getAllOrFail($filter = array())
    {
        $instance = new static;
        $result = $instance->findAll($filter);
        if(is_null($result)){
            Nayo_Exception::throw("Data Not Found", null);
        }
    }

    public function findAll($filter = array())
    {

        $htmlspeciachars = Config::AppConfig()['xss_security'];

        $db_result = new DBResults($this->table, $filter);
        $fields = $db_result->getFields();
        $results = $db_result->getAllData();

        $clsList = new Clslist(new $this);

        foreach ($results as $result) {
            $object = new $this;
            foreach ($result as $key => $row) {
                $type = "string";
                if(array_key_exists($key, $fields)){
                    $type = $fields[$key]['type'];
                }


                // if (preg_match("/^int/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (int)htmlspecialchars($row) : null) : (!empty($row) ? (int)$row : null) ;
                // } else if (preg_match("/^tinyint/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (int)htmlspecialchars($row) : null) : (!empty($row) ? (int)$row : null) ;
                // } else if (preg_match("/^decimal/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (float)htmlspecialchars($row) + 0.01 : null) : (!empty($row) ? (float)$row + 0.01 : null) ;
                // } else if (preg_match("/^double/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (double)htmlspecialchars($row) : null) : (!empty($row) ? (double)$row : null) ;
                // }else if(preg_match("/^smallint/", $type)){
                //     if(substr($type, 8, 3) == "(1)"){
                //         $object->$key = $htmlspeciachars == true ? (!empty($row) ? true : false) : (!empty($row) ? true : false) ;
                //     } else {
                //         $object->$key = $htmlspeciachars == true ? (!empty($row) ? (int)htmlspecialchars($row) : null) : (!empty($row) ? (int)$row : null) ;
                //     }
                // } else {
                    $object->$key = $htmlspeciachars == true ? htmlspecialchars($row) : $row ;
                // }
            }
            // array_push($this->results, $object);
            $clsList->add($object);
        }
        return $clsList->collections();
        // return $this->re;
    }

    public static function getOne($filter = array())
    {
        $instance = new static;
        return $instance->findOne($filter);
    }

    public static function getOneOrNew($filter = array()){
        $instance = new static;
        return $instance->findOne($filter, true);
    }

    public static function getOneOrFail($filter = array()){
        $instance = new static;
        $result = $instance->findOne($filter);

        if(is_null($result)){
            Nayo_Exception::throw("Data Not Found", null);
        }
    }

    public function findOne($filter = array(), $withNewObject = false)
    {
        $result = $this->findAll($filter);
        if (count($result) > 0)
            return $result[0];

        if($withNewObject)
            return $this;

        return null;
    }

    public static function get($id)
    {
        $instance = new static;
        return $instance->find($id);
    }

    public static function getOrNew($id)
    {
        $instance = new static;
        return $instance->find($id, true);
    }

    public static function getOrFail($id)
    {
        $instance = new static;
        $result = $instance->find($id);

        if(is_null($result)){
            Nayo_Exception::throw("Data Not Found", null);
        }
    }

    public function find($id, $withNewObject = false)
    {

        $htmlspeciachars = Config::AppConfig()['xss_security'];

        $db_result = new DBResults($this->table);
        $fields = $db_result->getFields();
        $result = $db_result->getById($id);
        if ($result) {

            $object = new $this;
            foreach ($result as $key => $row) {
                $type = "string";
                if(array_key_exists($key, $fields)){
                    // echo json_encode($fields[$key]);
                    $type = $fields[$key]['type'];
                }

                // if (preg_match("/^int/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (int)htmlspecialchars($row) : null) : (!empty($row) ? (int)$row : null) ;
                // } else if (preg_match("/^tinyint/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (int)htmlspecialchars($row) : null) : (!empty($row) ? (int)$row : null) ;
                // } else if (preg_match("/^decimal/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (float)htmlspecialchars($row) + 0.01 : null) : (!empty($row) ? (float)$row  + 0.01  : null) ;
                // } else if (preg_match("/^double/", $type)){
                //     $object->$key = $htmlspeciachars == true ? (!empty($row) ? (double)htmlspecialchars($row) : null) : (!empty($row) ? (double)$row : null) ;
                // }else if(preg_match("/^smallint/", $type)){
                //     if(substr($type, 8, 3) == "(1)"){
                //         $object->$key = $htmlspeciachars == true ? (!empty($row) ? true : false) : (!empty($row) ? true : false) ;
                //     } else {
                //         $object->$key = $htmlspeciachars == true ? (!empty($row) ? (int)htmlspecialchars($row) : null) : (!empty($row) ? (int)$row : null) ;
                //     }
                // } else {
                    $object->$key = $htmlspeciachars == true ? htmlspecialchars($row) : $row ;
                // }
            }
            return $object;
        } 
        if($withNewObject)
            return $this;

        return null;
    }

    //Overriding method
    public function beforeSave()
    { }

    public function save()
    {
        $db_result = new DBResults($this->table);
        $fields = $db_result->getFields();

        $this->beforeSave();
        $newId = false;
        if (!isset($this->Id)) {

            if (in_array("Created", $fields))
                $this->Created = mysqldatetime();
            $newId = $db_result->insert($this);
        } else {

            if (in_array("Modified", $fields))
                $this->Modified = mysqldatetime();

            $newId = $db_result->update($this);
        }

        return $newId;
    }

    public static function getOneAndRemove($id){

        $instance = new static;
        $result = $instance->find($id);
        if($result->delete());
            return $result;
        return false;
    }

    public static function remove($id){
        $instance = new static;
        $result = $instance->find($id);
        return $result->delete();

    }

    public function delete()
    {
        $db_result = new DBResults($this->table);
        return $db_result->delete($this->Id);
    }

    

    public function __call($name, $argument)
    {
        if (substr($name, 0, 4) == 'get_' && substr($name, 4, 5) != 'list_' && substr($name, 4, 6) != 'first_') {
            $sufixColumn = isset($argument[0]) ? "_{$argument[0]}" : null;
            $entity = 'App\\Models\\' . table_entity(substr($name, 4));
            $field = substr($name, 4) . '_Id'. $sufixColumn;
            $entityobject = $entity;
            if (!empty($this->$field)) {
                $result = $entityobject::getOrNew($this->$field);
                return $result;
            } else {
                return new $entityobject;
            }
        } else if (substr($name, 0, 4) == 'get_' && substr($name, 4, 5) == 'list_') {

            $params = isset($argument[0]) ? $argument[0] : null;

            $entity = 'App\\Models\\' . table_entity(substr($name, 9));
            $field = entity_entity($this->table) . '_Id';
            if (!empty($this->Id)) {
                $entityobject = $entity;

                if (isset($params['where'])) {
                    $params['where'][$field] = $this->Id;
                } else {
                    $params['where'] = [
                        $field => $this->Id
                    ];
                }

                $result = $entityobject::getAll($params);
                return $result;
            }
            return array();
        } else if (substr($name, 0, 4) == 'get_' && substr($name, 4, 6) == 'first_') {

            $params = isset($argument[0]) ? $argument[0] : null;
            $entity = 'App\\Models\\' . table_entity(substr($name, 10));
            $field = entity_entity($this->table) . '_Id';

            $entityobject = $entity;
            if (!empty($this->Id)) {

                if (isset($params['where'])) {
                    $params['where'][$field] = $this->Id;
                } else {
                    $params['where'] = [
                        $field => $this->Id
                    ];
                }
                $result = $entityobject::getOneOrNew($params);
                return $result;
            }

            return new $entityobject;
        } else {
            trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
        }
    }
}


