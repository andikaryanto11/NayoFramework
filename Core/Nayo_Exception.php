<?php
namespace Core;

use Exception;

class Nayo_Exception extends Exception{
    public $messages;
    public $data;
    public $status;
    public $redirectto ;

    public function __construct($messages, $data = array(), $status = array(), $redirectto = null){
        parent::__construct();
        $this->messages = $messages;
        $this->data = $data;
        $this->status = $status;
        $this->redirectto = $redirectto;
    }

    public static function throw($messages, $data = array(), $status = array(), $redirectto = null){
        throw new Nayo_Exception($messages, $data, $status, $redirectto);
    }

    public static function errorHandler($level, $messages, $file, $line)
    {
        // echo $file;
        if (error_reporting() !== 0) {  // to keep the @ operator working
            throw new \ErrorException($messages, 0, $level, $file, $line);
        }
    }

    public static function exceptionHandler($exception)
    {
        // Code is 404 (not found) or 500 (general error)
        $code = $exception->getCode();
        if ($code != 404) {
            $code = 500;
        }
        http_response_code($code);
        // echo "error";
        // echo $code;
        // if (\App\Config::SHOW_ERRORS) {
        //     echo "<h1>Fatal error</h1>";
        //     echo "<p>Uncaught exception: '" . get_class($exception) . "'</p>";
        //     echo "<p>Message: '" . $exception->getMessage() . "'</p>";
        //     echo "<p>Stack trace:<pre>" . $exception->getTraceAsString() . "</pre></p>";
        //     echo "<p>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "</p>";
        // } else {
            // $log = dirname(__DIR__) . '/logs/' . date('Y-m-d') . '.txt';
            // ini_set('error_log', $log);

            echo "<div style= 'border-style: solid;border-color: red; padding : 0px 50px; margin:0 50px;'>";
            echo "<br><b>A PHP Error was enccounter</b></br>";
            echo "<br>Error Code : '" . $code . "'</br>";
            echo "<br>Uncaught exception: '" . get_class($exception) . "'</br>";
            echo "<br>Message : '" . $exception->getMessage() . "'</br>";

            $i=0;
            $stacktrace = explode("#", $exception->getTraceAsString());
            echo "<br>File : '" . $stacktrace[1] . "'</br>";

            echo "<br>Stack trace: </br>";
            foreach($stacktrace as $trace){
                if($i <= 1 ){}
                else
                    echo "<div style = 'padding : 0px 0px 0px 50px'><br>".$trace."</br></div>";
                
                $i++;
            }
            echo "<br>Thrown in '" . $exception->getFile() . "' on line " . $exception->getLine() . "'</br>";
            // $message = "Error Code : '" . $code . "'";
            // $message .= "Uncaught exception: '" . get_class($exception) . "'";
            // $message .= " with message '" . $exception->getMessage() . "'";
            // $message .= "\nStack trace: " . $exception->getTraceAsString();
            // $message .= "\nThrown in '" . $exception->getFile() . "' on line " . $exception->getLine();

            // error_log($message);
            // set_error_handler()

            // echo $message;
            // View::renderTemplate("$code.html");
        // }
    }
}