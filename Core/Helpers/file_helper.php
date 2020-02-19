<?php

/**
 * @param array $files $_FILES['name']
 * @return array array of object
 */
function getArrayFiles_file(array $files){

    $length = count($files['name']);
    $result = array();
    for($i = 0 ; $i<$length; $i++){
        $arr = array(
            'name' => $files['name'][$i],
            'type' => $files['type'][$i],
            'tmp_name' => $files['tmp_name'][$i],
            'error' => $files['error'][$i],
            'size' => $files['size'][$i]
        );
        $result[] = $arr;
    }
    
    return $result;

}