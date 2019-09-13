<?php
namespace Core\Database\PDO;

use Core\Interfaces\IDbDriver;
use Core\Database\Connection;
use \PDO;

class PDOMySQL implements IDbDriver{

    protected static $instance = false;

    public $conn = false;  //DB connection resources

    protected $sql;  
    
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
        $sql = "DESC ". $table;
        $result = $this->query($sql, false)->fetch();
        $pk;
        if($result){
            foreach ($result as $v) {

                $fields['column'] = $v['Field'];

                if ($v['Key'] == 'PRI') {
                    $fields['primary'] = true;
                    // If there is PK, save it in $pk
                    $this->pk = $v['Field'];
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
        return $this->statement->fetchAll(PDO::FETCH_ASSOC);
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