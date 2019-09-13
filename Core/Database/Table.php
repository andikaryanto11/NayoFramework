<?php
namespace Core\Database;
use Core\Database\Connection;

class Table {

    protected $sql = "";
    protected $table = "";
    protected $connection  = false;
    protected $addedcolumns = array();
    protected $droppedcolumns = array();
    protected $seeds = array();
    protected $foreignKey = array();
    protected $creation = "CREATE TABLE @table ( @columns )";
    protected $seed = "INSERT INTO @table ( @columns ) VALUES ( @values )";
    protected $alter = "ALTER TABLE @table @columns ";
    protected $primaryKey = "";
    protected $db = false;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        if(!$this->connection)
            $this->connection = new Connection();
        
        if(!$this->db){
            $drivertype = !empty($this->connection->getDriverType()) ? $this->connection->getDriverType()."\\" : "Driver\\";
            $ent = "Core\\Database\\".$drivertype.$this->connection->drivers()[$this->connection->getDriverClass()];
            // echo $ent;
            $this->db = $ent::getInstance();
            // echo $this->connection->drivers()[$this->connection->getDriverClass()];
        }
    }

    public function table($table){
        $this->table = $table;
        return $this;
    }

    public function addColumn($name, $type, $length = "", $isNull = false, $default = null, $isPrimary = false, $isAutoIncrement = false, $after = null){
        
        $column = [
            'Name' => $name,
            'Type' => $type,
            'Length' => $length,
            'IsNull' => $isNull,
            'Default' => $default,
            'IsPrimary' => $isPrimary,
            'IsAutoIncrement' => $isAutoIncrement,
            'After' => $after
        ];

        array_push($this->addedcolumns, $column);
        return $this;

    }

    public function dropColumn($name){

    }

    public function addForeignKey($column, $tableRef, $keyRef, $onDelete = "RESTRICT", $onUpdate = "CASCADE" ){
        $foreignKey = [
            'Column' => $column,
            'TableRef' => $tableRef,
            'KeyRef' => $keyRef,
            'OnDelete' => $onDelete,
            'OnUpdate' => $onUpdate
        ];

        array_push($this->foreignKey, $foreignKey);
        return $this;
    }

    public function create(){

        if($this->connection->getDriverClass() == 'mysql' || $this->connection->getDriverClass() == 'mysqli'){

            $primarykey = "PRIMARY KEY ( @key )";
            $columns = array();
            foreach($this->addedcolumns as $column){

                if($column['IsPrimary']){
                    $this->primaryKey = $column['Name'];
                }

                $auotincrement = "";
                $null = "";
                $default = "";
                $length = "";

                if($column['IsAutoIncrement'])
                    $auotincrement = "AUTO_INCREMENT";

                // if(!$column['IsNull'])
                //     $null = "NOT NULL";
                
                if(!empty($column['Length']))
                    $length = "({$column['Length']})";
                
                if(!is_null($column['Default']))
                    $default = "DEFAULT '{$column['Default']}'";
                else {

                    if(!$column['IsNull'])
                        $null = "NOT NULL";
                    else 
                        $null = "DEFAULT NULL";

                }

                array_push($columns, "`{$column['Name']}` {$column['Type']}{$length} {$null} {$default} {$auotincrement}\n");

            }
            
            if(!empty($this->primaryKey))
                array_push($columns, str_replace("@key", $this->primaryKey, $primarykey));

            // addding foreignkey
            foreach($this->foreignKey as $key => $foreignkey) {
                $foreign = (object)$foreignkey;
                
                array_push($columns, "KEY `{$this->table}_{$foreign->Column}_fk` (`{$foreign->Column}`)\n");
                array_push($columns, "CONSTRAINT `{$this->table}_{$foreign->Column}_fk` FOREIGN KEY (`{$foreign->Column}`) REFERENCES `{$foreign->TableRef}` (`{$foreign->KeyRef}`) ON UPDATE {$foreign->OnUpdate} ON DELETE {$foreign->OnDelete}\n");
            }

            $strColumn = implode(",", $columns);

            $creation = str_replace(["@table", "@columns"], ["`".$this->table."`", $strColumn], $this->creation);

            $charset = !empty($this->connection->charset) ? $this->connection->charset : "utf8";
            $engine = !empty($this->connection->engine) ? $this->connection->engine : "InnoDB";

            $creation .= " ENGINE = {$engine} DEFAULT CHARSET = {$charset}";
            // echo $creation;
            $this->db->query($creation);
            // $this->db->close();

        } else if($this->connection->getDriverClass() == 'sqlsrv'){

            $columns = array();
            foreach($this->addedcolumns as $column){
                

                $isPrimary = "";
                $auotincrement = "";
                $null = "";
                $default = "";
                $length = "";

                
                if($column['IsPrimary']){
                    $isPrimary = "PRIMARY KEY";
                }

                if($column['IsAutoIncrement'])
                    $auotincrement = "IDENTITY(1,1)";

                // if(!$column['IsNull'])
                //     $null = "NOT NULL";
                
                if(strtolower($column['Type']) != 'int' && 
                    strtolower($column['Type']) != 'smallint' &&
                    strtolower($column['Type']) != 'tinyint')
                    if(!empty($column['Length']))
                        $length = "({$column['Length']})";
                
                if(!is_null($column['Default']))
                    $default = "DEFAULT ('{$column['Default']}')";
                else {

                    if(!$column['IsNull'])
                        $null = "NOT NULL";
                    else 
                        $null = "DEFAULT NULL";

                }
                array_push($columns, "[{$column['Name']}] {$column['Type']}{$length} {$auotincrement} {$null} {$default} {$isPrimary}\n");

            }

            foreach($this->foreignKey as $key => $foreignkey) {
                $foreign = (object)$foreignkey;
                
                array_push($columns, "CONSTRAINT {$this->table}_{$foreign->Column}_fk FOREIGN KEY ({$foreign->Column}) REFERENCES {$foreign->TableRef} ({$foreign->KeyRef}) ON UPDATE {$foreign->OnUpdate} ON DELETE {$foreign->OnDelete}\n");
            }
            
            $strColumn = implode(",", $columns);

            $creation = str_replace(["@table", "@columns"], [$this->table, $strColumn], $this->creation);
            // echo $creation;
            $this->db->query($creation);
            // $this->db->close();
        }

    }

    public function alter(){

        $columns = array();
        
        if($this->connection->getDriverClass() == 'mysql' || $this->connection->getDriverClass() == 'mysqli'){

            foreach($this->addedcolumns as $column){
                
                if($column['IsPrimary']){
                    $this->primaryKey = $column['Name'];
                }
    
                $auotincrement = "";
                $null = "";
                $default = "";
                $length = "";
    
                if($column['IsAutoIncrement'])
                    $auotincrement = "AUTO_INCREMENT";
    
                // if(!$column['IsNull'])
                //     $null = "NOT NULL";
                
                if(!empty($column['Length']))
                    $length = "({$column['Length']})";
                
                if(!is_null($column['Default']))
                    $default = "DEFAULT '{$column['Default']}'";
                else {
    
                    if(!$column['IsNull'])
                        $null = "NOT NULL";
                    else 
                        $null = "DEFAULT NULL";
    
                }

                $after = "";
                if(!is_null($column['After']))
                    $after = "AFTER `{$column['After']}`";
    
                array_push($columns, "ADD COLUMN `{$column['Name']}` {$column['Type']}{$length} {$null} {$default} {$auotincrement} {$after}\n");
    
            }
            foreach($this->droppedcolumns as $column){
    
                array_push($columns, "DROP COLUMN `{$column['Name']}`\n");
    
            }

            if(!empty($this->primaryKey))
                array_push($columns, "ADD PRIMARY KEY (`{$this->primaryKey}`)");

            foreach($this->foreignKey as $key => $foreignkey) {
                $foreign = (object)$foreignkey;
                
                array_push($columns, "ADD CONSTRAINT `{$this->table}_{$foreign->Column}_fk` FOREIGN KEY (`{$foreign->Column}`) REFERENCES `{$foreign->TableRef}` (`{$foreign->KeyRef}`) ON UPDATE {$foreign->OnUpdate} ON DELETE {$foreign->OnDelete}\n");
            }

            $strColumn = implode(",", $columns);
            $alter = str_replace(["@table", "@columns"], ["`".$this->table."`", $strColumn], $this->alter);
            // echo $alter;
            $this->db->query($alter);
            // $this->db->close();
            

        } else if($this->connection->getDriverClass() == 'sqlsrv'){

            foreach($this->addedcolumns as $column){
                
                if($column['IsPrimary']){
                    $this->primaryKey = $column['Name'];
                }
    
                $auotincrement = "";
                $null = "";
                $default = "";
                $length = "";
    
                if($column['IsAutoIncrement'])
                    $auotincrement = "AUTO_INCREMENT";
    
                // if(!$column['IsNull'])
                //     $null = "NOT NULL";
                
                if(strtolower($column['Type']) != 'int' && 
                    strtolower($column['Type']) != 'smallint' &&
                    strtolower($column['Type']) != 'tinyint')
                        if(!empty($column['Length']))
                            $length = "({$column['Length']})";
                
                if(!is_null($column['Default']))
                    $default = "DEFAULT '{$column['Default']}'";
                else {
    
                    if(!$column['IsNull'])
                        $null = "NOT NULL";
                    else 
                        $null = "DEFAULT NULL";
    
                }

                array_push($columns, "ADD [{$column['Name']}] {$column['Type']}{$length} {$null} {$default} {$auotincrement}\n");
    
            }
            foreach($this->droppedcolumns as $column){
    
                array_push($columns, "DROP COLUMN [{$column['Name']}]\n");
    
            }

            if(!empty($this->primaryKey))
                array_push($columns, "ADD PRIMARY KEY ([{$this->primaryKey}])");

            foreach($this->foreignKey as $key => $foreignkey) {
                $foreign = (object)$foreignkey;
                
                array_push($columns, "ADD CONSTRAINT {$this->table}_{$foreign->Column}_fk FOREIGN KEY ({$foreign->Column}) REFERENCES {$foreign->TableRef} ({$foreign->KeyRef}) ON UPDATE {$foreign->OnUpdate} ON DELETE {$foreign->OnDelete}\n");
            }

            foreach($columns as $column){

                $alter = str_replace(["@table", "@columns"], ["[".$this->table."]", $column], $this->alter);
                // echo $alter;
                $this->db->query($alter);
            }
            // $this->db->close();
        }
    }

    public function addSeed($column, $value){

        $seeds = [
            'Column' => $column,
            'Value' => $value
        ];

        array_push($this->seeds, $seeds);
        return $this;
    }

    public function seeds(){
        $columns = array();
        $values = array();

        if($this->connection->getDriverClass() == 'mysql' || $this->connection->getDriverClass() == 'mysqli'){
            
            foreach($this->seeds as $seed){
                array_push($columns, "`".$seed['Column']."`");
                array_push($values, "'".$seed['Value']."'");
            }


            $strcolumn = implode(",", $columns);
            $strvalue = implode(",", $values);
            $seed = str_replace(["@table", "@columns", "@values"], ["`".$this->table."`", $strcolumn, $strvalue], $this->seed);

            // echo $seed;
            $this->db->query($seed);
            // $this->db->close();
            $this->seeds = array();

        } else if($this->connection->getDriverClass() == 'sqlsrv'){ 

            foreach($this->seeds as $seed){
                array_push($columns, "[".$seed['Column']."]");
                array_push($values, "'".$seed['Value']."'");
            }

            $strcolumn = implode(",", $columns);
            $strvalue = implode(",", $values);

            $seed = str_replace(["@table", "@columns", "@values"], [$this->table, $strcolumn, $strvalue], $this->seed);
            // echo $seed;
            $this->db->query($seed);
            // $this->db->close();
            $this->seeds = array();
        }
    }
}