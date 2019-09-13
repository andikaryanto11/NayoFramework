<?php
namespace Core\Database;
use Core\Database\Database;
use Core\Database\Connection;
use Core\Database\Table;

class Seed {

    protected $connection = false;
    protected $db = false;
    public $enable_auto_seed = FALSE;
    protected $files;
    protected $version = array();
    protected $countMigrated = 0;
    public function __construct(){

        include "App\Config\Config.php";

        $this->enable_auto_seed = $config['enable_auto_seed'];

        Connection::init();

        if(!$this->db){
            $drivertype = !empty(Connection::getDriverType()) ? Connection::getDriverType()."\\" : "Driver\\";
            $ent = "Core\\Database\\".$drivertype.Connection::drivers()[Connection::getDriverClass()];
            // echo $ent;
            $this->db = $ent::getInstance();
            // echo Connection::$drivers()[Connection::$getDriverClass()];
        }
        
        if($this->enable_auto_seed)
            if(!$this->isTableExist('seeds'))
                $this->createSeedTable();

        $this->files = $this->readSeedDatabaseFile();
        $this->version = $this->getSeedVersion();
    }

    public function seedAll(){

        foreach($this->files as $file){
            $this->seed($file);
        }        

        echo "seed count : ". $this->countMigrated."\n";
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

    private function createSeedTable(){
        $table = new Table();
        $table->table("seeds");
        $table->addColumn("Id","int", "11", false, null, true, true);
        $table->addColumn("Version", "Varchar", "50", true);
        $table->addColumn("ExecutedAt", "DATETIME", "", true);
        $table->create();
        // $sql = "
        //     CREATE TABLE `seeds` (
        //     `Id` int(11) NOT NULL AUTO_INCREMENT,
        //     `Version` varchar(50) DEFAULT NULL,
        //     `ExecutedAt` DATETIME DEFAULT NULL,
        //     PRIMARY KEY (`Id`)
        //   ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        // ";

        // $result = $this->db->query($sql);

    }

    public function seed(string $version){

        if(!in_array(explode("_",$version)[1], $this->version)) {
            $dbversion = explode("_",$version);
            require_once APP_PATH. "Database\\Seeds\\".$version.".php";

            $path = "App\\Database\\Seeds\\".$version;
            $seed = new $path;
            $seed->up();
            echo $version. " : seeded successfuly \n";
            $this->countMigrated ++;
            $this->insertSeed($dbversion[1]);
        } else {
        }

    }

    private function readSeedDatabaseFile(){
        $path = APP_PATH . "Database/Seeds/";
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

    private function getSeedVersion(){
        $sql = "SELECT * FROM seeds";
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

    private function insertSeed($dbversion){

        $datenow = mysqldatetime();
        if(Connection::getDriverClass() == 'mysql' || Connection::getDriverClass() == 'mysqli'){

            $insertversion = "INSERT INTO seeds VALUES(null, '{$dbversion}', '{$datenow}')";
            $result = $this->db->query($insertversion);
            
        } else if (Connection::getDriverClass() == 'sqlsrv'){

            $insertversion = "INSERT INTO seeds (Version, ExecutedAt) VALUES('{$dbversion}', '{$datenow}')";
            $result = $this->db->query($insertversion);

        }
    }
}   