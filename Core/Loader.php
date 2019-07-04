<?php
namespace Core;

class Loader{

// Load library classes

    // public function coreLibrary($libs = array()){

    //     foreach($libs as $lib){
    //         require LIB_PATH . ucfirst("$lib.php");
    //     }

    // }

    public function appLibrary($libs = array()){

        foreach($libs as $lib){
            require LIB_PATH . ucfirst("$lib.php");
        }

    }

    public function coreThirdParty($thirds = array()){
        foreach($thirds as $third){
            include THIRD_PARTY_PATH . ucfirst("$third.php");
        }
    }


    // loader helper functions. Naming conversion is xxx_helper.php;

    public function coreHelper($helpers = array()){

        foreach($helpers as $helper){
            include HELPER_PATH . "{$helper}_helper.php";
        }

    }

    
    public function appHelper($helpers = array()){

        foreach($helpers as $helper){
            include APP_HELPER_PATH . "{$helper}_helper.php";
        }

    }

    public function readControllers(){
        $path = APP_PATH . "Controllers/";
        $version = array();
        if ($handle = opendir($path)) {

            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && $entry != "index.html" && $entry != "Rests") {
                    // echo $ent
                    include $path.$entry;
                    // echo $entry;
                }
            }
        
            // require $path.$handle;
        }
    }

}