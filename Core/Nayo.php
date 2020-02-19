<?php

use Core\Loader;

class Nayo{
    protected static $controller = "";
    protected static $action = "";
    protected static $routes = array();
    protected static $instance;
    private static $args = array();

    public function __construct(){
        
    }

    public static function run($argv){
        error_reporting(E_ALL); 
        ini_set('display_errors', 1);
        self::$instance = new self;

        if(empty($argv)){
            self::$instance->init();

            self::$instance->autoload();

            self::$instance->autoloadfile();

            // self::$instance->migrate();

            self::$instance->dispatch();
            
        } else {

            self::$instance->define();
            self::$instance->autoload();
            self::$instance->autoloadfile();

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

    public  function init(){
        // Define path constants
        self::$instance->define();        
    }

    public  function define(){

        define("DS", DIRECTORY_SEPARATOR);

        define("ROOT", getcwd() . DS);
        
        define("BASE_PATH", ROOT. DS);
        
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

        require BASE_PATH.'vendor/autoload.php';

        // load config
        include CONFIG_PATH . "Config.php";
        global $GLOBALS;
        $GLOBALS['config'] = $config;
        self::checkAppKey();
       
        // Start session
        session_start();
    }

    private  function autoload() {
        spl_autoload_register(function ($class_name) {
            include_once(ROOT . str_replace("\\", "/", $class_name) . '.php');
        });
    }

    private  function autoloadfile(){
        require CONFIG_PATH . "Autoload.php";
        
        $loader = new Loader();
        $loader->coreHelper(array('config','url', 'language', 'helper', 'inflector', 'string', 'file', 'currency', 'form'));
        $loader->appHelper($autoload['helper']);
        $loader->appClasses($autoload['classes']);
    }

    private  function dispatch() {

        require(APP_PATH."Config/Routes.php");
 
    }

    private  function checkAppKey(){
        if(empty($GLOBALS['config']["app_key"]))
            die("Application Key Is Not Set, Please Set It First Before Run Your Application");
    }
}