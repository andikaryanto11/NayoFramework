<?php

use Core\Loader;

class Nayo{
    protected static $controller = "";
    protected static $action = "";
    protected static $routes = array();
    private static $args = array();

    public function __construct(){
        
    }

    public static function run($argv){

        if(empty($argv)){
            self::init();

            self::autoload();

            self::autoloadfile();

            // self::migrate();

            self::dispatch();
            
        } else {

            self::define();
            self::autoloadfile();

            $function = "";
            $params = array();
            $i = 0;
            foreach ($argv AS $arg){
                if($i > 0){
                    if($i == 1){
                        $function = $arg;
                    } else {
                        $params[] = $arg;
                    }
                }
                $i++;
            }
            
            call_user_func_array(array("Core\\CLI", $function), $params);
            
        }
    }

    public function model(){
        
    }

    public static function init(){
        // Define path constants
        self::define();        
    }

    public static function define(){


        define("APP_PATH", ROOT . 'App' . DS);

        define("CORE_PATH", ROOT . "Core" . DS);

        define("PUBLIC_PATH", ROOT . "Public" . DS);

        define("CONFIG_PATH", APP_PATH . "Config" . DS);

        define("CONTROLLER_PATH", APP_PATH . "Controllers" . DS);

        define("MODEL_PATH", APP_PATH . "Models" . DS);

        define("VIEW_PATH", APP_PATH . "Views" . DS);

        define("APP_HELPER_PATH", APP_PATH . "Helpers" . DS);

        define("APP_LANGUAGE_PATH", APP_PATH . "Languages" . DS);

        define("LIB_PATH", APP_PATH . "Libraries" . DS);

        define('DB_PATH', CORE_PATH . "Database" . DS);

        define("HELPER_PATH", CORE_PATH . "Helpers" . DS);

        define("THIRD_PARTY_PATH", CORE_PATH . "ThirdParty" . DS);

        define("CORE_LANGUAGE_PATH", CORE_PATH . "Languages" . DS);

        define("CORE_CONFIG_PATH", CORE_PATH . "Config" . DS);

        define("UPLOAD_PATH", PUBLIC_PATH . "Uploads" . DS);

        define("CURR_CONTROLLER_PATH", CONTROLLER_PATH);

        define("CURR_VIEW_PATH", VIEW_PATH);

        define("APP_CACHE", APP_PATH . "Cache");

        define("CORE_BLADE", CORE_PATH . "Blade" . DS);

        define("BLADE_CACHE", CORE_BLADE . "Cache");


        // load config
        
        include CONFIG_PATH . "Config.php";
        global $GLOBALS;
        $GLOBALS['config'] = $config;

        


        // Start session

        session_start();
    }

    private static function autoload() {
        spl_autoload_register(array(__CLASS__,'load'));

    }

    private static function autoloadfile(){
        require CONFIG_PATH . "Autoload.php";
        
        include CORE_CONFIG_PATH . "Autoload.php";

        foreach($corenamespaces as $key => $corenamespace){
            foreach($corenamespace as $filename){
                
                require  ROOT . $key ."/".$filename. ".php";
                
            }
        }
        $loader = new Loader();
        // $loader->readControllers();
        $loader->coreHelper(array('url', 'language', 'helper', 'inflector', 'string', 'file', 'currency', 'form'));
        // $loader->coreLibrary(array('clslist','datatables','','ftp', 'file', 'helper'));
        $loader->appHelper($autoload['helper']);
        $loader->appLibrary($autoload['library']);
    }

    // Define a custom load method

    private static function load($classname){

        // Here simply autoload app’s controller and model classes
        // echo $classname;

        // if(explode("\\", $classname)[1] == "Models"){
        //     $name = explode("\\", $classname)[2];
        //     require_once MODEL_PATH . "$name.php";

        // } 

    }
 
    private static function dispatch() {
        // print_r($_GET);

        require(APP_PATH."Config/Routes.php");
 
    }

    private static function migrate(){

    }
}