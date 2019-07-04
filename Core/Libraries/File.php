<?php
namespace Core\Libraries;

class File {

    protected $filetype = array();
    protected $maxsize;
    protected $destination = false;

    public $errormsg = "";

    public function __construct($destination, array $filetype = array(), $maxsize = 0)
    {
        if(!$this->destination)
            $this->destination = $destination;

        $this->filetype = $filetype;
        $this->maxsize = $maxsize;
    }

    public function upload(array $files){

        if($this->maxsize != 0 && $files['size'] > $this->maxsize){

            $this->errormsg = clang('File.size_too_large');
            return false;
        }

        if(!in_array($this->getExtension($files), $this->filetype)){

            $this->errormsg = clang('File.extension_not_allowed');
            return false; 
        }
        
        if(move_uploaded_file($files['tmp_name'], ROOT.DS.$this->destination.DS.$files['name'])){
        
            return true;

        }

        return false;

    }

    public function getErrorMessage(){
        return $this->errormsg;
    }

    public function getExtension(array $files){
        return pathinfo($files['name'], PATHINFO_EXTENSION);
    }

    public function setExtension($fileType){
        if(is_array($fileType)){
            $this->filetype = $fileType;
            return $this;
        }

        $this->filetype = [];
        array_push($this->filetype, $fileType);
        return $this;

    }

    public function addExtension($fileType){
        array_push($this->filetype, $fileType);
        return $this;


    }
}