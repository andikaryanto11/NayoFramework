<?php
namespace Core\Libraries;

class Dictionary {
    
    private  $collections = array();

    public  function add($key, $object){

        $this->collections[$key] = $object;
        return $this;
    }

    /**
     * @return array array of object
     */
    public  function get(){
        return $this->collections;
    }

    /**
     * @return object first data of array
     */
    public  function first(){
        if($this->collections)
        $key = array_keys($this->collections)[0];
        $value = array_values($this->collections)[0];
        $arr[$key] = $value;
        return $arr;
    }

    /**
     * @return object last data of array
     */
    public  function last(){
       $arr = array_reverse($this->collections);
       $key = array_keys($arr)[0];
       $value = array_values($arr)[0];
       $data[$key] = $value;
       return $data;
    }

    /**
     * @return int count of array data
     */
    public  function count(){   
        return count($this->collections);
    }


    /**
     * @param callback $callback function condition in return
     * @return mixed value that found, null if not found
     */
    public function find($callback){
        if(!is_null($callback)){
            foreach ($this->collections as $key => $value) {
                if (call_user_func($callback, $value, $key)) {
                    return $value;
                }
            }
        }
    }

    public function where($callback){
        return array_filter($this->collections, $callback, ARRAY_FILTER_USE_BOTH);
    }
}