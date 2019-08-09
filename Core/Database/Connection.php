<?php
namespace Core\Database;

class Connection {

    public static $host;
    public static $user;
    public static $password;
    public static $dbname;
    public static $port;
    public static $charset;
    public static $engine;

    public static $connectionString = false;

    protected static $driver = false;

    protected static $conn = false;  //DB connection resources

    protected static $sql;           //sql statement

    public static $currentdb;

    public static $drivers = [
        'mssql' => 'PDOMsSQL',
        'mysql' => 'PDOMySQL',
        'mysqli' => 'Mysqli',
        'sqlsrv' => 'Sqlsrv'
    ];

   

    /**

     * Constructor, to connect to database, select database and set charset

     * @param $db string configuration array

     */

    public static function init(){
        require APP_PATH . "Config/Database.php";

        
        $connectionString = isset($db['default']['dbname'])? $db['default']['connectionstring'] : '';

        self::$host = isset($db['default']['host'])? $db['default']['host'] : 'localhost';

        self::$user = isset($db['default']['user'])? $db['default']['user'] : 'root';

        self::$password = isset($db['default']['password'])? $db['default']['password'] : '';

        self::$dbname = isset($db['default']['dbname'])? $db['default']['dbname'] : '';

        self::$driver = isset($db['default']['dbname'])? $db['default']['driver'] : '';

        self::$port = isset($db['default']['port'])? $db['default']['port'] : '3306';

        self::$charset = isset($db['default']['charset'])? $db['default']['charset'] : '';

        self::$engine = isset($db['default']['engine'])? $db['default']['engine'] : '';

        if(!self::$connectionString) {
            self::$connectionString = $connectionString;
        }

        if(!self::$driver) {
            self::$driver = $driver;
        }
        
    }

    public static function getConnectionString(){
        return self::$connectionString;
    }

    public static function getDriverClass(){
        return explode(".",self::$driver)[1];
    }

    public static function getDriverType(){
        return explode(".",self::$driver)[0];
    }

    public static function drivers(){
        return self::$drivers;
    }
}