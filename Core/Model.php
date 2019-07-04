<?php
namespace Core;
use Core\Database\DBResults;
use Core\Database\Connection;
use Core\Session;
use Core\Libraries\ClsList;

class Nayo_Model {
    protected $db = false;
    protected $db_result = false;
    protected $session = false;
    // protected $table = false;
    //filtering
    protected $append = "";
    protected $where = array();
    protected $order = array();
    protected $columnOpenMark = "`";
    protected $columnCloseMark = "`";
    protected $driverclass = "";

    protected $connection = false;

    public function __construct(){

        if(!$this->session)
            $this->session = new Session();

    }

    public function connection(){

        Connection::init();
        
        $this->driverclass = Connection::getDriverClass();
        // $this->driverclass = "mysqli";
        if($this->driverclass == "mysqli" || $this->driverclass == "mysql"){
            $this->columnOpenMark = "`";
            $this->columnCloseMark = "`";
        }
        else if($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
            $this->columnOpenMark = "[";
            $this->columnCloseMark = "]";
        }
    }

    public function count($filter = array()){
        $result = $this->findAll($filter);
        return count($result);
    }

    public function findAll($filter = array()){

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
        if($where)
            $this->where($where);
            
        if($wherein)
            $this->whereIn($wherein);
        
        if($wherenotin)
            $this->whereNotIn($wherenotin);
            
        if($orwhere)
            $this->orWhere($orwhere);

        if($like)
            $this->like($like);

        if($orlike)
            $this->orLike($orlike);
        
        if($group)
            $this->group($group);

        if($order)
            $this->orderBy($order);
        
        if($limit)
            $this->limit($limit);

        $db_result = new DBResults($this->table);
        
        $results = $db_result->getAllData($this->append);
        // echo $this->append;
        $this->append = "";

        $clsList = new ClsList(new $this);

        foreach ($results as $result){
            $object = new $this;
            foreach($result as $key => $row){
                $object->$key = $row;
            }
            // array_push($this->results, $object);
            $clsList->add($object);
        }
        return $clsList->collections();
        // return $this->re;
    }

    public function findOne($filter = array()){
        $result = $this->findAll($filter);
        if(count($result) > 0)
            return $result[0];
        return null;
    }

    public function find($id){

        $db_result = new DBResults($this->table);
        // $result = $this->db_result->getById($id);
        $result = $db_result->getById($id);
        if ($result){
            
            $object = new $this;
            foreach($result as $key => $row){
                $object->$key = $row;
            }
            return $object;
        }
        return null;
    }

    public function save(){
        $db_result = new DBResults($this->table);
        $fields = $db_result->getFields();

        $newId = false;
        if(!isset($this->Id)){

            if(in_array("Created", $fields))
                $this->Created = mysqldatetime();
            $newId = $db_result->insert($this);
        } else {
            
            if(in_array("Modified", $fields))
                $this->Modified = mysqldatetime();
                
            $newId = $db_result->update($this);
        }
        
        return $newId;
    }

    public function delete(){
        $db_result = new DBResults($this->table);
        // return $this->db_result->delete($this->Id);
        return $db_result->delete($this->Id);
        // return $this;
    }

    private function where($where){
        $this->connection();
        $qry="";
        if(count($this->where) == 0)
            $qry = " WHERE ";
        else 
            $qry = " AND ";
        
        $wheres = array();
        foreach($where as $k => $v){
            $newVal = escapeString($v);
            array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." = '{$newVal}'") ;
            array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)."= '{$newVal}'") ;
        }
        $this->append .= $qry.implode(" AND ", $wheres);
        // echo $this->append;
        return $this;
    }

    private function whereIn($whereIn){
        $this->connection();
        $qry="";
        if(count($this->where) == 0)
            $qry = " WHERE ";
        else 
            $qry = " AND ";

        $wheres = array();
        foreach($whereIn as $k => $v){
            $arrVal = array();
            foreach($v as $newVal){
                $arrVal[] = "'".escapeString($newVal)."'";
            }

            array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." IN (".implode(",", $arrVal).")");
            array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." IN (".implode(",", $arrVal).")");
        }

        $this->append .= $qry.implode(" AND ", $wheres);
        // echo $this->append;
        return $this;
    }

    private function orWhere($orwhere){
        $this->connection();
        $qry="";
        if(count($this->where) == 0)
            $qry = " WHERE ";
        else 
            $qry = " OR ";

        $wheres = array();

        foreach($orwhere as $k => $v){
            $newVal = escapeString($v);
            array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)."= '{$newVal}'") ;
            array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)."= '{$newVal}'") ;
        }

        $this->append .= $qry.implode(" OR ", $wheres);
        
        return $this;
    }

    private function whereNotIn($whereNotIn){
        $this->connection();

        $qry="";
        if(count($this->where) == 0)
            $qry = " WHERE ";
        else 
            $qry = " AND ";

        $wheres = array();
        foreach($whereNotIn as $k => $v){
            $arrVal = array();
            foreach($v as $newVal){
                $arrVal[] = "'".escapeString($newVal)."'";
            }

            array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." NOT IN (".implode(",", $arrVal).")");
            array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." NOT IN (".implode(",", $arrVal).")");
        }

        $this->append .= $qry.implode(" AND ", $wheres);
        // echo $this->append;
        return $this;
    }

    private function orderBy($order){
        $qry = " ORDER BY ";

        foreach($order as $k => $v){
            array_push($this->order, "{$k} {$v}") ;
        }

        $this->append .= $qry.implode(" , ", $this->order);
        
        return $this;
    }

    private function limit($limit){
        $this->connection();
        if($this->driverclass == 'mysqli' || $this->driverclass == 'mysql'){

            $qry = " LIMIT ";

            $this->append .= $qry. ($limit['page']-1) . ", ".$limit['size'];

        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
            $order = "";

            if(empty($this->order)){
                $order = " ORDER BY Id ASC ";
            }

            $qry = " OFFSET ";

            $this->append .= $order . $qry. ($limit['page']-1) . " ROWS FETCH NEXT {$limit['size']} ROWS ONLY";

        }
        return $this;
    }

    private function like($like){
        
        $this->connection();

        $qry = "";
        if(count($this->where) == 0)
            $qry = " WHERE ";
        else 
            $qry = " AND ";
        
        $wheres = array();

        if($this->driverclass == 'mysqli' || $this->driverclass == 'mysql'){
            foreach($like as $k => $v){
                if(is_array($v)){
                    $arrVal = [];
                    foreach($v as $newV){
                        $arrVal[] = escapeString($newV);
                    }
                    $regVal = implode("|", $arrVal);
                    array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                    array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                } else {

                    array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                    array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                }
            }
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
            foreach($like as $k => $v){
                if(is_array($v)){
                    $arrVal = [];
                    foreach($v as $newV){
                        
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                    }
                } else {

                    array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                    array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                }
            }
        }

        $this->append .= $qry.implode(" AND ", $wheres);
        return $this;
    }

    private function orLike($orlike){
        
        $this->connection();
        if(count($this->where) == 0)
            $qry = " WHERE ";
        else 
            $qry = " OR ";
        
        $wheres = array();

        if($this->driverclass == 'mysqli' || $this->driverclass == 'mysql'){
            foreach($orlike as $k => $v){
                if(is_array($v)){
                    $arrVal = [];
                    foreach($v as $newV){
                        $arrVal[] = escapeString($newV);
                    }
                    $regVal = implode("|", $arrVal);
                    array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                    array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                } else {

                    array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                    array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                }
            }
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
            foreach($orlike as $k => $v){
                if(is_array($v)){
                    $arrVal = [];
                    foreach($v as $newV){
                        
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                    }
                } else {

                    array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                    array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                }
            }
        }

        $this->append .= $qry.implode(" OR ", $wheres);
        return $this;
    }

    private function group($group){
        
        $this->connection();

        $qry="";
        if(isset($group['where']) && !empty($group['where'])){
            $where = $group['where'];

            if(count($this->where) == 0)
                $qry = " WHERE ";
            else 
                $qry = " AND ";
            
            $wheres = array();

            foreach($where as $k => $v){
                $newVal = escapeString($v);
                array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." = '{$newVal}'") ;
                array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)."= '{$newVal}'") ;
            }

            $this->append .= $qry.implode(" AND ", $wheres);
                // echo $this->append;
                
        }

        if(isset($group['orwhere']) && !empty($group['orwhere'])){
            $orwhere = $group['orwhere'];
            if(count($this->where) == 0)
                $qry = " WHERE ";
            else 
                $qry = " OR ";

            $wheres = array();

            foreach($orwhere as $k => $v){
                $newVal = escapeString($v);
                array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)."= '{$newVal}'") ;
                array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)."= '{$newVal}'") ;
            }

            $this->append .= $qry. " ( " . implode(" OR ", $wheres) . " ) ";
                
        }

        if(isset($group['orlike']) && !empty($group['orlike'])){
            $orlike = $group['orlike'];
            if(count($this->where) == 0)
                $qry = " WHERE ";
            else 
                $qry = " AND ";
            
            $wheres = array();

            if($this->driverclass == 'mysqli' || $this->driverclass == 'mysql'){
                foreach($orlike as $k => $v){
                    if(is_array($v)){
                        $arrVal = [];
                        foreach($v as $newV){
                            $arrVal[] = escapeString($newV);
                        }
                        $regVal = implode("|", $arrVal);
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                    } else {
    
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                    }
                }
            } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
                foreach($orlike as $k => $v){
                    if(is_array($v)){
                        $arrVal = [];
                        foreach($v as $newV){
                            
                            array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                            array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                        }
                    } else {
    
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                    }
                }
            }

            $this->append .= $qry." ( ". implode(" OR ", $wheres) ." ) ";
        }

        if(isset($group['like']) && !empty($group['like'])){
            $like = $group['like'];
            if(count($this->where) == 0)
                $qry = " WHERE ";
            else 
                $qry = " AND ";
            
            $wheres = array();

            if($this->driverclass == 'mysqli' || $this->driverclass == 'mysql'){
                foreach($like as $k => $v){
                    if(is_array($v)){
                        $arrVal = [];
                        foreach($v as $newV){
                            $arrVal[] = escapeString($newV);
                        }
                        $regVal = implode("|", $arrVal);
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$regVal}'") ;
                    } else {
    
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." REGEXP '{$v}'") ;
                    }
                }
            } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql'){
                foreach($orlike as $k => $v){
                    if(is_array($v)){
                        $arrVal = [];
                        foreach($v as $newV){
                            
                            array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                            array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$regVal}%'") ;
                        }
                    } else {
    
                        array_push($this->where, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                        array_push($wheres, $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark)." LIKE '%{$v}%'") ;
                    }
                }
            }

            $this->append .= $qry." ( ". implode(" AND ", $wheres) ." ) ";
        }

        return $this;

        
    }

    public function __call($name, $argument){
        // echo $name;

        if (substr($name, 0, 4) == 'get_' && substr($name, 4, 5) != 'list_' && substr($name, 4, 6) != 'first_' )
		{
			$entity = 'App\\Models\\'.table(substr($name, 4));
            $field = substr($name, 4).'_Id';
            
            $entityobject = new $entity;

			if(isset($this->$field)){
                $result = $entityobject->find($this->$field);
                return $result;
			} else {
				return $entityobject;
			}
			
		} else if (substr($name, 0, 4) == 'get_' && substr($name, 4, 5) == 'list_') {

            $params = isset($argument[0]) ? $argument[0] : null;

            $entity = 'App\\Models\\'.table(substr($name, 9));
            $field = entity($this->table).'_Id';
			if(isset($this->Id)){
                $entityobject = new $entity;

                if(isset($params['where'])){
                    $params['where'][$field] =$this->Id;
                } else {
                    $params['where'] = [
                        $field => $this->Id
                    ];
                }

                $result = $entityobject->findAll($params);
				return $result;
			}
            return array();

        } else if (substr($name, 0, 4) == 'get_' && substr($name, 4, 6) == 'first_') {

            $entity = 'App\\Models\\'.table(substr($name, 10));
            $field = entity($this->table).'_Id';

            $entityobject = new $entity;
			if(isset($this->Id)){
                $params = array(
                    'where' => array(
                        $field => $this->Id
                    )
                );
                $result = $entityobject->findOne($params, true);

				return $result;
            }
            
            return null;

		} else {
			trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
		}
        
    }

}


if (!defined('MYSQL_EMPTYDATE')) define('MYSQL_EMPTYDATE', '0000-00-00');
if (!defined('MYSQL_EMPTYDATETIME')) define('MYSQL_EMPTYDATETIME', '0000-00-00 00:00:00');

if (!function_exists('table'))
{
	function table($entity)
	{
		return pluralize($entity);
	}
}  

if (!function_exists('table'))
{
	function entity($table)
	{
        $word = titleize(singularize($table));
        $split = explode(" ", $word);
        return implode("_", $split);
	}
}  
    
