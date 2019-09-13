<?php 
namespace Core\Database\Driver;

use Core\Database\Databases;
use Core\Database\Connection;
use Core\Interfaces\IDbDriver;

class Sqlsrv implements IDbDriver{

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
        // echo $connection->host;
        // echo phpinfo();
        if (!$this->conn) {
            $connectionInfo = [
                'Database' => Connection::$dbname,
                'UID' => Connection::$user,
                'PWD' => Connection::$password
            ];
            $this->conn = sqlsrv_connect(Connection::$host, $connectionInfo ) ;//or die('Database connection error');
            if($this->conn ) {
                // echo "Connection established.<br />";
           }else{
                // echo "Connection could not be established.<br />";
                die( print_r( sqlsrv_errors(), true));
           }
            $this->currentdb = Connection::$dbname;
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

        $this->query($sql, false);
        // echo json_encode($result);
        $result = $this->fetch();
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
        // print_r($this->fields);
        // // If there is PK, add it into fields list
        // if (isset($pk)) {

        //     $this->fields['pk'] = $pk;

        // }

        return $this->fields;
    }

    public function pk(){
        return $this->pk;
    }
    
    /**
     * @param string $sql  
     * @return array
     * 
     */
    public function query($sql, $loging = true){        

        $this->sql = $sql;

        // Write SQL statement into log

        if($loging){
            $str = $sql . "  [". date("Y-m-d H:i:s") ."]" . PHP_EOL;
    
            file_put_contents("log.txt", $str, FILE_APPEND);
        }
        // echo $this->sql;
        $this->statement = sqlsrv_query($this->conn, $this->sql);
        // echo json_encode($this->statement);
        if (!$this->statement) {

            // die($this->errno().':'.$this->error().'<br />Error SQL statement is '.$this->sql.'<br />');
            $arr = [
                'errCode' => $this->errno(),
                'errMessage' => $this->error(),
                'errQuery' => $this->sql
            ];
            
            return $arr; //$this->errno().':'.$this->error().'\nError SQL statement is '.$this->sql.'\n';

        }
        return $this;

    }   
    
    public function multiQuery($sql, $loging = true)
    {
        
    }

    public function fetch(){
        // print_r($query);
        $list = array();
        if($this->statement){
            while ($row = sqlsrv_fetch_array($this->statement, SQLSRV_FETCH_ASSOC)){
                $list[] = $row;

            }
        }
        sqlsrv_free_stmt($this->statement);

        return $list;

    }

    public function fetchObject(){
        // print_r($query);
        $list = array();
        if($this->statement){
            while ($row = sqlsrv_fetch_array($this->statement, SQLSRV_FETCH_ASSOC)){
                $list[] = (object)$row;

            }
        }
        sqlsrv_free_stmt($this->statement);

        return $list;

    }

    public function getAll($sql){
        $this->query($sql);
        // print_r($query);
        $list = array();
        if($this->statement){
            while ($row = sqlsrv_fetch_array($this->statement, SQLSRV_FETCH_ASSOC)){
                $list[] = $row;

            }
        }
        sqlsrv_free_stmt($this->statement);

        return $list;

    }

    public function getOne($sql){
        $this->query($sql);
        $single;
        if($this->statement){
            $single = sqlsrv_fetch_array($this->statement, SQLSRV_FETCH_ASSOC);
        }
        sqlsrv_free_stmt($this->statement);
        return $single;

    }

    public function getStatement(){
        return $this->statement;
    }

    /**

     * Get last insert id

     */

    public function getInsertId(){
        
        sqlsrv_next_result($this->statement);
        sqlsrv_fetch($this->statement);
        return sqlsrv_get_field($this->statement, 0);
    }

    /**

     * Get error number

     * @access private

     * @return error number

     */

    public function errno(){

        return null;

    }

    /**

     * Get error message

     * @access private

     * @return error message

     */

    public function error(){

        return sqlsrv_errors(SQLSRV_ERR_ALL);

    }

    public function close(){
        sqlsrv_close($this->conn);
    }

    public function beginTransaction()
    {
        mysqli_begin_transaction($this->conn);
    }
    
    public function commit()
    {
        mysqli_commit($this->conn);
    }
    
    public function rollback()
    {
        mysqli_rollback($this->conn);
    }
}