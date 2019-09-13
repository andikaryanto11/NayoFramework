<?php 
namespace Core\Database\Driver;

use Core\Database\Databases;
use Core\Database\Connection;
use Core\Interfaces\IDbDriver;

class Mssql implements IDbDriver{

    protected static $instance = false;

    public $conn = false;  //DB connection resources

    protected $sql;           //sql statement

    public $currentdb;
    /**
     * Class constructor.
     */
    public function __construct()
    {

        Connection::init();

        if (!$this->conn) {
            $this->conn = mssql_connect(Connection::$host, Connection::$user, Connection::$password, Connection::$dbname) or die('Database connection error');
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
        $sql = "DESC ". $table;
        $result = $this->getAll($sql);
        $pk;
        if($result){
            foreach ($result as $v) {

                $this->fields[] = $v['Field'];

                if ($v['Key'] == 'PRI') {

                    // If there is PK, save it in $pk

                    $pk = $v['Field'];

                }

            }
        }
        // If there is PK, add it into fields list
        if (isset($pk)) {

            $this->fields['pk'] = $pk;

        }

        return $this->fields;
    }

    public function pk(){
        return $this->fields['pk'];
    }
    
    /**
     * @param string $sql  
     * @return array
     * 
     */
    public function query($sql){        

        $this->sql = $sql;
        // Write SQL statement into log

        $str = $sql . "  [". date("Y-m-d H:i:s") ."]" . PHP_EOL;

        file_put_contents("log.txt", $str, FILE_APPEND);

        $result = mysqli_query($this->conn, $this->sql);
        
        if (! $result) {

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

        
    }
    
    public function fetchObject(){

        
    }

    public function getAll($sql){
        $query = $this->query($sql);
        
        $list = array();
        if($query){
            while ($row = mysqli_fetch_assoc($query)){
                $list[] = $row;

            }
        }
        mysqli_free_result($query);

        return $list;

    }

    public function getOne($sql){
        $query = $this->query($sql);
        
        $single;
        if($query){
            $single = mysqli_fetch_assoc($query);
        }

        mysqli_free_result($query);

        return $single;

    }

    public function getStatement(){
        return null;
    }

    /**

     * Get last insert id

     */

    public function getInsertId(){

        return mysqli_insert_id($this->conn);

    }

    /**

     * Get error number

     * @access private

     * @return error number

     */

    public function errno(){

        return mysqli_errno($this->conn);

    }

    /**

     * Get error message

     * @access private

     * @return error message

     */

    public function error(){

        return mysqli_error($this->conn);

    }

    public function close(){
        mysqli_close($this->conn);
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