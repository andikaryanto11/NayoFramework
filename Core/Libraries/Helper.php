<?php
namespace Core\Libraries;

class Helper {

    public static function arr_filter($array, $key, $value){
        $data = [];

        foreach($array as $v){
            foreach($v as $k => $val){
                if($k == $key && $val == $value)
                    $data[] = $v;
            }
                
        }

        return $data;

    }
}