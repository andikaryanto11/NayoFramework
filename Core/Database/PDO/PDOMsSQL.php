<?php
namespace Core\Database\PDO;

use Core\Interfaces\IDbDriver;
use Core\Database\Connection;
use \PDO;

class PDOMsSQL implements IDbDriver{

    protected static $instance = false;

    public $conn = false;  //DB connection resources

    protected $sql;           //sql statement

    protected $fields = array();

    protected $pk;

    public $currentdb;

    protected $statement;

    /**
     * Class constructor.
     */
    private function __construct()
    {
        Connection::init();

        if(!$this->conn){
            try {
                $this->conn = new PDO(Connection::$connectionString, Connection::$user, Connection::$password);
                $this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
            } catch (PDOException $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
                die();
            }
        }
    }

    public static function getInstance(){
        if(!self::$instance)
            self::$instance = new static;
    
        return self::$instance;
    }

    public function getConnection(){
        return $this->conn;
    }

    public function getFields($table)
    {
        $sql = "SELECT A.COLUMN_NAME , isnull(OBJECTPROPERTY(OBJECT_ID(CONSTRAINT_SCHEMA + '.' + QUOTENAME(CONSTRAINT_NAME)), 'IsPrimaryKey'),0) as IS_PRIMARY 
        FROM INFORMATION_SCHEMA.COLUMNS A
        LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE B ON B.COLUMN_NAME = A.COLUMN_NAME 
            AND OBJECTPROPERTY(OBJECT_ID(B.CONSTRAINT_SCHEMA + '.' + QUOTENAME(B.CONSTRAINT_NAME)), 'IsPrimaryKey') = 1
            AND B.TABLE_NAME = N'$table'
        WHERE A.TABLE_NAME = N'$table'";

        $result = $this->query($sql, false)->fetch();
        $pk;
        if($result){
            foreach ($result as $v) {

                $fields['column'] = $v['COLUMN_NAME'];

                if ($v['IS_PRIMARY']) {

                    // If there is PK, save it in $pk
                    $fields['primary'] = true;
                    $this->pk = $v['COLUMN_NAME'];
                    // $pk = $v['COLUMN_NAME'];

                } else 
                    $fields['primary'] = false;

                $this->fields[] = $fields;
            }
        }

        return $this->fields;
    }

    public function pk(){
        return $this->pk;
    }

    public function query($sql, $loging = true){
        $this->sql = $sql;

        // Write SQL statement into log

        if($loging){
            $str = $sql . "  [". date("Y-m-d H:i:s") ."]" . PHP_EOL;
    
            file_put_contents("log.txt", $str, FILE_APPEND);
        }

        $this->statement = $this->conn->prepare($sql);
        $this->statement->execute();
        return $this;
        // return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function multiQuery($sql, $loging = true)
    {
    }

    public function fetch(){
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchObject(){
        return $this->statement->fetchAll(PDO::FETCH_CLASS);
    }

    public function getAll($sql){
        $this->query($sql);
        $result = $this->statement->fetchAll(PDO::FETCH_ASSOC);
        // $this->close();
        return $result;
    }

    public function getOne($sql){
        $this->query($sql);
        $result = $this->statement->fetchAll(PDO::FETCH_ASSOC);

        if($result)
        {
            return $result[0];
        }
        return $result;
    }

    public function getStatement(){
        return $this->statement;
    }

    public function getInsertId(){
        return $this->conn->lastInsertId();
    }

    public function errno()
    {
        return $this->conn->errorCode();
    }

    public function error()
    {
        return $this->conn->errorInfo();
    }

    public function close(){
        $this->conn = null;
    }

    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }
    
    public function commit()
    {
        $this->conn->commit();
    }
    
    public function rollback()
    {
        $this->conn->rollBack();
    }
}