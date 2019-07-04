<?php

function escapeString(string $string){
    return str_replace("'", "''", $string);
}

function columnValidate(string $string, $openmark, $closeMark){
    $phrase = str_replace(" ", "", $string);

    $cond = ["!", "<>", "<", ">"];

    foreach($cond as $needle){
        if(strpos($phrase, $needle)){
            return str_replace($needle,$closeMark.$needle, $phrase);
        }
    }

    return $phrase.$closeMark;
}