<?php
namespace Core\Libraries;
use Core\Nayo_Exception;
use Core\Interfaces\IClsList;

class Clslist implements IClsList {
    public $type;
    private $collection = array();

    public function __construct($object){

        $this->type =  get_class($object);
    }

    public function add($object){
        if($this->isValidClass($object)){
            $this->collection[] = $object;
        } else {
            // throw new \Exception;
            try {
                throw new \Exception(sprintf(clang('Error.could_not_add_list_type_of'), $this->type, get_class($object)));
            } catch(\Exception $e) {
                // Nayo_Exception::errorHandler(1, $e->getMessage(), $e->getFile(), $e->getLine());
                Nayo_Exception::exceptionHandler($e);
            }
           
            exit;
        }

        return $this;
    }

    private function isValidClass($object){
        if($this->type == get_class($object))
        // if($this->type instanceof $object)
            return true;
        return false;
    }

    public function collections(){
        return $this->collection;
    }

    public function getCollectionType(){
        return $this->type;
    }

    public function count(){
        return count($this->collection);
    }

    public function where($field){

    }
}