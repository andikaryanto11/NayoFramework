<?php
namespace Core\Database;
use Core\Database\Connection;

class Query {

    public $mainsql = "";
    public $sqlcondition = "";

    protected $where = array();
    protected $order = array();
    protected $columnOpenMark = "`";
    protected $columnCloseMark = "`";
    protected $driverclass = "";

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

    public function getSql(){
        return $this->mainsql . $this->sqlcondition;
    }

    public function insertInto($table, $object){
        $field_list = array();  //field list string

        $value_list = array();  //value list string

        foreach($object as $key => $value){
            if(is_bool($value)){
                $field_list[] = "{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark, false);
                $value_list[] = $value == true ? 1 : 0;
            }

            if(!empty($value) ){
                $field_list[] = "{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark, false);
                $value_list[] = "'".escapeString($value)."'";
            }
                
        }

        return $this->insertIntoTable($table, $field_list, $value_list);
       
    }

    public function insertIntoTable($table, $fieldList, $valueList){
        $lastid = "";
        $this->mainsql = "INSERT INTO {$table} (".implode(",",$fieldList).") VALUES(".implode(",",$valueList).")".$lastid;
        return $this->getSql();
    }

    public function update($table, $object){
        $list = array();
        foreach($object as $key => $value){
            if($key != "Id")
                if(!empty($value)){
                    $list[] ="{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark) . " '".escapeString($value)."'";
                } else {
                    if(is_bool($value)){
                        $newval = $value == true ? 1 : 0;
                        $list[] ="{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark) . " {$newval}";
                    } else {

                    $list[] ="{$this->columnOpenMark}".columnValidate($key, $this->columnOpenMark, $this->columnCloseMark) . " NULL";
                    }
                }
                
        }

        return $this->updateTable($table, $list, $object->Id);
    }

    public function updateTable($table, $valueList, $id){
        $this->mainsql = "UPDATE {$table} SET ".implode(",",$valueList)." WHERE Id = ".$id;
        return $this->getSql();
    }

    public function delete($table, $id){

        $this->mainsql = "DELETE FROM {$table} WHERE Id = ".$id;
        return $this->getSql();
    }

    public function selectCount($table, $condition = []){
        $this->mainsql = "SELECT COUNT(*) as Counted FROM `{$table}` ";
        if($condition)
            $this->appendCondition($condition);
        return $this->getSql();
    }

    public function selectFromtable($table, $condition = []){
        $this->mainsql = "SELECT `{$table}`.* FROM `{$table}` ";
        if($condition)
            $this->appendCondition($condition);
        return $this->getSql();
    }

    public function select($fields){
        $this->mainsql = "SELECT {$fields} ";
        return $this;
    }

    public function from($table){
        $this->mainsql .= "FROM {$table} ";
        return $this;
    }

    public function appendCondition($filter){

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

        return $this;
    }

    public function join($join){
        $this->connection();
        $qry = "";
        $key = "";
        foreach($join as $k => $d){
            
            foreach($d as $v){
                $as = isset($v['as']) ? $v['as'] : $k;

                if(isset($v['type'])){
                    $qry .= strtoupper($v['type']). " JOIN ";
                } else {
                    $qry .= "INNER JOIN ";
                }

                $qry .= $this->columnOpenMark.columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false)." {$as} ON ".$this->columnOpenMark.columnValidate($as.".Id", $this->columnOpenMark, $this->columnCloseMark) .$this->columnOpenMark.columnValidate("{$v['table']}.{$v['column']}", $this->columnOpenMark, $this->columnCloseMark, false);
            }
        }
        $this->sqlcondition .= $qry;
        return $this;

    }

    public function where($where)
    {
        $this->connection();
        $qry = "";
        if (count($this->where) == 0)
            $qry = " WHERE ";
        else
            $qry = " AND ";

        $wheres = array();
        foreach ($where as $k => $v) {
            if(is_bool($v)){
                $newVal = $v == true ? 1 : 0;
                array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
            }
            else {
                if (!empty($v)) {
                    if($v == 'null'){
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                    } else if ($v == 'not null'){
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                    } else {
                        $newVal = escapeString($v);
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                    }
                }
            }
        }

        if (!empty($wheres))
            $this->sqlcondition .= $qry . implode(" AND ", $wheres);
        // echo $this->sqlcondition;
        return $this;
    }

    public function whereIn($whereIn)
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
            $this->sqlcondition .= $qry . implode(" AND ", $wheres);
        return $this;
    }

    public function orWhere($orwhere)
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
                if($v == 'null'){
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                } else if ($v == 'not null'){
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                } else {
                    $newVal = escapeString($v);
                    array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                    array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                }
            }
        }

        if (!empty($wheres))
            $this->sqlcondition .= $qry . implode(" OR ", $wheres);

        return $this;
    }

    public function whereNotIn($whereNotIn)
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
            $this->sqlcondition .= $qry . implode(" AND ", $wheres);
        // echo $this->sqlcondition;
        return $this;
    }

    public function orderBy($order)
    {
        $qry = " ORDER BY ";

        foreach ($order as $k => $v) {
            array_push($this->order, "{$k} {$v}");
        }

        $this->sqlcondition .= $qry . implode(" , ", $this->order);

        return $this;
    }

    public function limit($limit)
    {
        $this->connection();
        if ($this->driverclass == 'mysqli' || $this->driverclass == 'mysql') {

            if($limit['page'] && $limit['size']){
                $qry = " LIMIT ";
                $this->sqlcondition .= $qry . ($limit['page'] - 1) . ", " . $limit['size'];
            }
        } else if ($this->driverclass == 'sqlsrv' || $this->driverclass == 'mssql') {
            $order = "";

            if (empty($this->order)) {
                $order = " ORDER BY Id ASC ";
            }

            $qry = " OFFSET ";

            $this->sqlcondition .= $order . $qry . ($limit['page'] - 1) . " ROWS FETCH NEXT {$limit['size']} ROWS ONLY";
        }
        return $this;
    }

    public function like($like)
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
            $this->sqlcondition .= $qry . implode(" AND ", $wheres);
        return $this;
    }

    public function orLike($orlike)
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

        $this->sqlcondition .= $qry . implode(" OR ", $wheres);
        return $this;
    }

    public function group($group)
    {

        $this->connection();

        $beginstartgroup = false;
        $qry = "";
        if (isset($group['where']) && !empty($group['where'])) {
            $where = $group['where'];
            $startgroup = $beginstartgroup == false ? "( " : "";
            if (count($this->where) == 0)
                $qry = " WHERE " . $startgroup;
            else
                $qry = " AND " . $startgroup;

            $beginstartgroup = true;
            $wheres = array();

            foreach ($where as $k => $v) {
                if (!empty($v)) {
                    if($v == 'null'){
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                    } else if ($v == 'not null'){
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                    } else {
                        $newVal = escapeString($v);
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                    }
                }
            }
            
            if (!empty($wheres))
                $this->sqlcondition .= $qry . implode(" AND ", $wheres);
            // echo $this->sqlcondition;

        }

        if (isset($group['orWhere']) && !empty($group['orWhere'])) {
            

            $orwhere = $group['orWhere'];
            $startgroup = $beginstartgroup == false ? "( " : "";
            if (count($this->where) == 0)
                $qry = " WHERE ". $startgroup;
            else
                $qry = " OR ". $startgroup;

            $beginstartgroup = true;
            $wheres = array();

            foreach ($orwhere as $k => $v) {
                if (!empty($v)) {
                    if($v == 'null'){
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NULL");
                    } else if ($v == 'not null'){
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark, false) . " IS NOT NULL");
                    } else {
                        $newVal = escapeString($v);
                        array_push($this->where, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                        array_push($wheres, $this->columnOpenMark . columnValidate($k, $this->columnOpenMark, $this->columnCloseMark) . "'{$newVal}'");
                    }
                }
            }

            if (!empty($wheres))
                $this->sqlcondition .= $qry . implode(" OR ", $wheres) ;
        }

        if (isset($group['orLike']) && !empty($group['orLike'])) {
            $orlike = $group['orLike'];

            $startgroup = $beginstartgroup == false ? "( " : "";
            if (count($this->where) == 0)
                $qry = " WHERE ". $startgroup;
            else
                $qry = " AND ". $startgroup;

            $beginstartgroup = true;
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

            $this->sqlcondition .= $qry . " ( " . implode(" OR ", $wheres) . " ) ";
        }

        if (isset($group['like']) && !empty($group['like'])) {
            $like = $group['like'];

            $startgroup = $beginstartgroup == false ? "( " : "";
            if (count($this->where) == 0)
                $qry = " WHERE ". $startgroup ;
            else
                $qry = " AND ". $startgroup;

            $beginstartgroup = true;
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

            $this->sqlcondition .= $qry . " ( " . implode(" AND ", $wheres) . " ) ";
        }

        $this->sqlcondition = " {$this->sqlcondition} )";

        return $this;
    }

    public function startGroup(){
        $this->sqlcondition .= " ( ";
        return $this;
    }

    public function endGroup(){
        $this->sqlcondition .= " ) ";
        return $this;
    }
}