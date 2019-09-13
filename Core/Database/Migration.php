<?php
namespace Core\Database;
use Core\Database\Database;
use Core\Database\Connection;
use Core\Database\Table;

class Migration {

    protected $connection = false;
    protected $db = false;
    public $enable_auto_migration = FALSE;
    protected $files;
    protected $version = array();
    protected $countMigrated = 0;
    public function __construct(){

        include "App\Config\Config.php";

        $this->enable_auto_migration = $config['enable_auto_migration'];

        Connection::init();

        if(!$this->db){
            $drivertype = !empty(Connection::getDriverType()) ? Connection::getDriverType()."\\" : "Driver\\";
            $ent = "Core\\Database\\".$drivertype.Connection::drivers()[Connection::getDriverClass()];
            // echo $ent;
            $this->db = $ent::getInstance();
            // echo Connection::$drivers()[Connection::$getDriverClass()];
        }
        
        if($this->enable_auto_migration)
            if(!$this->isTableExist('migrations'))
                $this->createMigrationTable();

        $this->files = $this->readMigrationDatabaseFile();
        $this->version = $this->getMigrationVersion();
    }

    public function migrateAll(){

        foreach($this->files as $file){
            $this->migrate($file);
        }        

        echo "migration count : ". $this->countMigrated."\n";
    }

    public function isTableExist(string $table){
        $sql = "SELECT count(*) as Count
        FROM information_schema.TABLES
        WHERE (TABLE_SCHEMA = '{$this->db->currentdb}') AND (TABLE_NAME = '{$table}')";

        $result = $this->db->getOne($sql);
        // $data = mysqli_fetch_assoc($result);
        if($result['Count'] > 0){
            return true;
        }

        return false;
    }

    private function createMigrationTable(){
        $table = new Table();
        $table->table("migrations");
        $table->addColumn("Id","int", "11", false, null, true, true);
        $table->addColumn("Version", "Varchar", "50", true);
        $table->addColumn("ExecutedAt", "DATETIME", "", true);
        $table->create();
        // $sql = "
        //     CREATE TABLE `migrations` (
        //     `Id` int(11) NOT NULL AUTO_INCREMENT,
        //     `Version` varchar(50) DEFAULT NULL,
        //     `ExecutedAt` DATETIME DEFAULT NULL,
        //     PRIMARY KEY (`Id`)
        //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        // ";

        // $result = $this->db->query($sql);

    }

    public function migrate(string $version){

        if(!in_array(explode("_",$version)[1], $this->version)) {
            $dbversion = explode("_",$version);
            require_once APP_PATH. "Database\\Migrations\\".$version.".php";

            $path = "App\\Database\\Migrations\\".$version;
            $migration = new $path;
            $migration->up();
            echo $version. " : migrated successfuly \n";
            $this->countMigrated ++;
            $this->insertMigration($dbversion[1]);
        } else {
        }

    }

    private function readMigrationDatabaseFile(){
        $path = APP_PATH . "Database/Migrations/";
        $version = array();
        if ($handle = opendir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    // echo $entry;
                    array_push($version, explode(".", $entry)[0]);
                }
            }
        
            closedir($handle);
        }
        return $version;
    }

    private function getMigrationVersion(){
        $sql = "SELECT * FROM migrations";
        $result = $this->db->getAll($sql);
        if($result){
            $data = array();
            foreach($result as $res){
                array_push($data, $res['Version']);
            }
            return $data;
        }
        return array();
    }  

    private function insertMigration($dbversion){

        $datenow = mysqldatetime();
        if(Connection::getDriverClass() == 'mysql' || Connection::getDriverClass() == 'mysqli'){

            $insertversion = "INSERT INTO migrations VALUES(null, '{$dbversion}', '{$datenow}')";
            $result = $this->db->query($insertversion);
            // $this->db->close();
            
        } else if (Connection::getDriverClass() == 'sqlsrv' || Connection::getDriverClass() == 'mssql'){

            $insertversion = "INSERT INTO migrations (Version, ExecutedAt) VALUES('{$dbversion}', '{$datenow}')";
            $result = $this->db->query($insertversion);
            // $this->db->close();

        }
    }
}   