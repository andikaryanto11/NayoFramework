<?php

namespace Core;

use Core\Database\DBResults;
use Core\Database\Connection;
use Core\Session;
use Core\Libraries\ClsList;
use Exception;

class Nayo_Model
{
    protected $db = false;
    protected $db_result = false;
    // protected $table = false;
    //filtering
    protected $append = "";
    protected $where = array();
    protected $order = array();
    protected $columnOpenMark = "`";
    protected $columnCloseMark = "`";
    protected $driverclass = "";

    protected $connection = false;

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

    public function connection()
    {

        Connection::init();

        $this->driverclass = Connection::getDriverClass();
        // $this->driverclass = "mysqli";
        if ($this->driverclass == "mysqli" || $this->driverclass == "mysql") {
            $this->columnOpenMark = "`";
            $this->columnCloseMark = "`";
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
            $this->columnOpenMark = "[";
            $this->columnCloseMark = "]";
        }
    }

    public static function countAll($filter = array())
    {
        $instance = new static;
        return $instance->count($filter);
    }

    public function count($filter = array())
    {
        $result = $this->findAll($filter);
        return count($result);
    }

    public static function getAll($filter = array())
    {
        $instance = new static;
        return $instance->findAll($filter);
    }

    public function findAll($filter = array())
    {

        $join = (isset($filter['join']) ? $filter['join'] : FALSE);
        $where = (isset($filter['where']) ? $filter['where'] : FALSE);
        $wherein = (isset($filter['whereIn']) ? $filter['whereIn'] : FALSE);
        $orwhere = (isset($filter['orWhere']) ? $filter['orWhere'] : FALSE);
        $wherenotin = (isset($filter['whereNotIn']) ? $filter['whereNotIn'] : FALSE);
        $like = (isset($filter['like']) ? $filter['like'] : FALSE);
        $orlike = (isset($filter['orLike']) ? $filter['orLike'] : FALSE);
        $order = (isset($filter['order']) ? $filter['order'] : FALSE);
        $limit = (isset($filter['limit']) ? $filter['limit'] : FALSE);
        $group = (isset($filter['group']) ? $filter['group'] : FALSE);

        // echo json_encode($where);
        if ($join)
            $this->join($join);

        if ($where)
            $this->where($where);

        if ($wherein)
            $this->whereIn($wherein);

        if ($wherenotin)
            $this->whereNotIn($wherenotin);

        if ($orwhere)
            $this->orWhere($orwhere);

        if ($like)
            $this->like($like);

        if ($orlike)
            $this->orLike($orlike);

        if ($group)
            $this->group($group);

        if ($order)
            $this->orderBy($order);

        if ($limit)
            $this->limit($limit);

        $db_result = new DBResults($this->table);

        $results = $db_result->getAllData($this->append);
        // echo $this->append;
        $this->append = "";

        $clsList = new ClsList(new $this);

        foreach ($results as $result) {
            $object = new $this;
            foreach ($result as $key => $row) {
                $object->$key = $row;
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

    public function findOne($filter = array())
    {
        $result = $this->findAll($filter);
        if (count($result) > 0)
            return $result[0];
        return null;
    }

    public static function get($id)
    {
        $instance = new static;
        return $instance->find($id);
    }

    public function find($id)
    {

        $db_result = new DBResults($this->table);
        // $result = $this->db_result->getById($id);
        $result = $db_result->getById($id);
        if ($result) {

            $object = new $this;
            foreach ($result as $key => $row) {
                $object->$key = $row;
            }
            return $object;
        }
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

    public function delete()
    {
        $db_result = new DBResults($this->table);
        // return $this->db_result->delete($this->Id);
        return $db_result->delete($this->Id);
        // return $this;
    }

    private function join($join){
        $this->connection();
        $qry = "";
        foreach($join as $k => $v){
            if(isset($v['type'])){
                $qry .= strtoupper($v['type']). " JOIN ";
            } else {
                $qry .= "INNER JOIN ";
            }

            $qry .= $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false)." ON ".$this->columnOpenMark.columnValidate($k.".Id", $this->columnOpenMark, $this->columnCloseMark) .$this->columnOpenMark.columnValidate("{$v['table']}.{$v['column']}", $this->columnOpenMark, $this->columnCloseMark, false);
        }
        $this->append .= $qry;
        return $this;

    }

    private function where($where)
    {
        $this->connection();
        $qry = "";
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " AND ";

        $wheres = array();
        foreach ($where as $k => $v) {
            if (!empty($v)) {
                $newVal = escapeString($v);
                array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
            }
        }

        if (!empty($wheres))
            $this->append .= $qry . implode(" AND ", $wheres);
        // echo $this->append;
        return $this;
    }

    private function whereIn($whereIn)
    {
        $this->connection();
        $qry = "";
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " AND ";

        $wheres = array();
        foreach ($whereIn as $k => $v) {
            $arrVal = array();
            if(!empty($v))
                foreach ($v as $newVal) {
                    if (!empty($newVal))
                        $arrVal[] = "'" . escapeString($newVal) . "'";
                }
            if (!empty($arrVal)) {
                array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IN (" . implode(",", $arrVal) . ")");
                array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IN (" . implode(",", $arrVal) . ")");
            }
        }
        if (!empty($wheres))
            $this->append .= $qry . implode(" AND ", $wheres);
        // echo $this->append;
        return $this;
    }

    private function orWhere($orwhere)
    {
        $this->connection();
        $qry = "";
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " OR ";

        $wheres = array();

        foreach ($orwhere as $k => $v) {
            if (!empty($v)) {
                $newVal = escapeString($v);
                array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
            }
        }

        if (!empty($wheres))
            $this->append .= $qry . implode(" OR ", $wheres);

        return $this;
    }

    private function whereNotIn($whereNotIn)
    {
        $this->connection();

        $qry = "";
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " AND ";

        $wheres = array();
        foreach ($whereNotIn as $k => $v) {
            $arrVal = array();
            if(!empty($v))
                foreach ($v as $newVal) {
                    if (!empty($newVal))
                        $arrVal[] = "'" . escapeString($newVal) . "'";
                }

            if (!empty($arrVal)) {
                array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " NOT IN (" . implode(",", $arrVal) . ")");
                array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " NOT IN (" . implode(",", $arrVal) . ")");
            }
        }

        if (!empty($wheres))
            $this->append .= $qry . implode(" AND ", $wheres);
        // echo $this->append;
        return $this;
    }

    private function orderBy($order)
    {
        $qry = " ORDER BY ";

        foreach ($order as $k => $v) {
            array_push($this->order, "{$k} {$v}");
        }

        $this->append .= $qry . implode(" , ", $this->order);

        return $this;
    }

    private function limit($limit)
    {
        $this->connection();
        if ($this->driverclass == 'mysqli' || $this->driverclass == 'mysql') {

            $qry = " LIMIT ";

            $this->append .= $qry . ($limit['page'] - 1) . ", " . $limit['size'];
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
            $order = "";

            if (empty($this->order)) {
                $order = " ORDER BY Id ASC ";
            }

            $qry = " OFFSET ";

            $this->append .= $order . $qry . ($limit['page'] - 1) . " ROWS FETCH NEXT {$limit['size']} ROWS ONLY";
        }
        return $this;
    }

    private function like($like)
    {

        $this->connection();

        $qry = "";
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " AND ";

        $wheres = array();

        if ($this->driverclass == 'mysqli' || $this->driverclass == 'mysql') {
            foreach ($like as $k => $v) {
                if (is_array($v)) {
                    $arrVal = [];
                    foreach ($v as $newV) {
                        $arrVal[] = escapeString($newV);
                    }
                    $regVal = implode("|", $arrVal);
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                } else {

                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                }
            }
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
            foreach ($like as $k => $v) {
                if (is_array($v)) {
                    $arrVal = [];
                    foreach ($v as $newV) {

                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                    }
                } else {

                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$v}%'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$v}%'");
                }
            }
        }

        if (!empty($wheres))
            $this->append .= $qry . implode(" AND ", $wheres);
        return $this;
    }

    private function orLike($orlike)
    {

        $this->connection();
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " OR ";

        $wheres = array();

        if ($this->driverclass == 'mysqli' || $this->driverclass == 'mysql') {
            foreach ($orlike as $k => $v) {
                if (is_array($v)) {
                    $arrVal = [];
                    foreach ($v as $newV) {
                        $arrVal[] = escapeString($newV);
                    }
                    $regVal = implode("|", $arrVal);
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                } else {

                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                }
            }
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
            foreach ($orlike as $k => $v) {
                if (is_array($v)) {
                    $arrVal = [];
                    foreach ($v as $newV) {

                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                    }
                } else {

                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . " LIKE '%{$v}%'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . " LIKE '%{$v}%'");
                }
            }
        }

        $this->append .= $qry . implode(" OR ", $wheres);
        return $this;
    }

    private function group($group)
    {

        $this->connection();

        $qry = "";
        if (isset($group['where']) && !empty($group['where'])) {
            $where = $group['where'];

            if (count($this->where) == 0)
                $qry = " WHERE ";
            else
                $qry = " AND ";

            $wheres = array();

            foreach ($where as $k => $v) {
                if (!empty($v)) {
                    $newVal = escapeString($v);
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                }
            }
            
            if (!empty($wheres))
                $this->append .= $qry . implode(" AND ", $wheres);
            // echo $this->append;

        }

        if (isset($group['orwhere']) && !empty($group['orwhere'])) {
            $orwhere = $group['orwhere'];
            if (count($this->where) == 0)
                $qry = " WHERE ";
            else
                $qry = " OR ";

            $wheres = array();

            foreach ($orwhere as $k => $v) {
                if (!empty($v)) {
                    $newVal = escapeString($v);
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                }
            }

            if (!empty($wheres))
                $this->append .= $qry . " ( " . implode(" OR ", $wheres) . " ) ";
        }

        if (isset($group['orlike']) && !empty($group['orlike'])) {
            $orlike = $group['orlike'];
            if (count($this->where) == 0)
                $qry = " WHERE ";
            else
                $qry = " AND ";

            $wheres = array();

            if ($this->driverclass == 'mysqli' || $this->driverclass == 'mysql') {
                foreach ($orlike as $k => $v) {
                    if (is_array($v)) {
                        $arrVal = [];
                        foreach ($v as $newV) {
                            $arrVal[] = escapeString($newV);
                        }
                        $regVal = implode("|", $arrVal);
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                    } else {

                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                    }
                }
            } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
                foreach ($orlike as $k => $v) {
                    if (is_array($v)) {
                        $arrVal = [];
                        foreach ($v as $newV) {

                            array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                            array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                        }
                    } else {

                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$v}%'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$v}%'");
                    }
                }
            }

            $this->append .= $qry . " ( " . implode(" OR ", $wheres) . " ) ";
        }

        if (isset($group['like']) && !empty($group['like'])) {
            $like = $group['like'];
            if (count($this->where) == 0)
                $qry = " WHERE ";
            else
                $qry = " AND ";

            $wheres = array();

            if ($this->driverclass == 'mysqli' || $this->driverclass == 'mysql') {
                foreach ($like as $k => $v) {
                    if (is_array($v)) {
                        $arrVal = [];
                        foreach ($v as $newV) {
                            $arrVal[] = escapeString($newV);
                        }
                        $regVal = implode("|", $arrVal);
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$regVal}'");
                    } else {

                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " REGEXP '{$v}'");
                    }
                }
            } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
                foreach ($orlike as $k => $v) {
                    if (is_array($v)) {
                        $arrVal = [];
                        foreach ($v as $newV) {

                            array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                            array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$regVal}%'");
                        }
                    } else {

                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$v}%'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " LIKE '%{$v}%'");
                    }
                }
            }

            $this->append .= $qry . " ( " . implode(" AND ", $wheres) . " ) ";
        }

        return $this;
    }

    public function __call($name, $argument)
    {
        // echo $name;

        if (substr($name, 0, 4) == 'get_' && substr($name, 4, 5) != 'list_' && substr($name, 4, 6) != 'first_') {
            $entity = 'App\\Models\\' . table(substr($name, 4));
            $field = substr($name, 4) . '_Id';
            $entityobject = $entity;
            if (!empty($this->$field)) {
                $result = $entityobject::get($this->$field);
                return $result;
            } else {
                return new $entityobject;
            }
        } else if (substr($name, 0, 4) == 'get_' && substr($name, 4, 5) == 'list_') {

            $params = isset($argument[0]) ? $argument[0] : null;

            $entity = 'App\\Models\\' . table(substr($name, 9));
            $field = entity($this->table) . '_Id';
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
            $entity = 'App\\Models\\' . table(substr($name, 10));
            $field = entity($this->table) . '_Id';

            $entityobject = $entity;
            if (!empty($this->Id)) {

                if (isset($params['where'])) {
                    $params['where'][$field] = $this->Id;
                } else {
                    $params['where'] = [
                        $field => $this->Id
                    ];
                }
                $result = $entityobject::getOne($params, true);
                if($result)
                    return $result;
                else 
                    return new $entityobject;
            }

            return new $entityobject;
        } else {
            trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
        }
    }
}


if (!defined('MYSQL_EMPTYDATE')) define('MYSQL_EMPTYDATE', '0000-00-00');
if (!defined('MYSQL_EMPTYDATETIME')) define('MYSQL_EMPTYDATETIME', '0000-00-00 00:00:00');

if (!function_exists('table')) {
    function table($entity)
    {
        return pluralize($entity);
    }
}

if (!function_exists('table')) {
    function entity($table)
    {
        $word = titleize(singularize($table));
        $split = explode(" ", $word);
        return implode("_", $split);
    }
}
