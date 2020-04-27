<?php
namespace Core\Database;
use Core\Database\Database;
use Core\Database\Connection;
use Core\Database\Table;
use Core\System\Config;

class Seed {

    protected $connection = false;
    protected $db = false;
    public $enable_auto_seed = FALSE;
    protected $files;
    protected $version = array();
    protected $countMigrated = 0;
    public function __construct(){

        $config = Config::AppConfig();

        $this->enable_auto_seed = $config['enable_auto_seed'];

        Connection::init();

        if(!$this->db){
            $this->db = Connection::getDriver();
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

    }

    public function seed(string $version){

        if(!in_array(explode("_",$version)[1], $this->version)) {
            $dbversion = explode("_",$version);
            include APP_PATH. "Database/Seeds/".$version.".php";

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
                    array_push($version, explode(".", $entry)[0]);
                }
            }
        
            closedir($handle);
        }
        asort($version);
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