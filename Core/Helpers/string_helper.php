<?php

function escapeString(string $string)
{
    return str_replace("'", "''", $string);
}

function columnValidate(string $string, $openMark, $closeMark, $isUseSymbol = true)
{
    $equal = "=";
    $phrase = str_replace([" ", "."], ["", "{$closeMark}.{$openMark}"], $string);

    $cond = ["!", "<>", "<", ">", "<=", ">="];

    if ($isUseSymbol){
        foreach ($cond as $needle) {

            if (strrpos($phrase, $needle) > 0) {
                if (preg_match("/(<|>|<=|>=)$/", $phrase) > 0) {
                    return str_replace($needle, $closeMark . $needle, $phrase);
                } else {
                    return str_replace($needle, $closeMark . $needle.$equal, $phrase);
                }
            } else {
                if (preg_match("/(<|>|<=|>=)$/", $phrase) > 0) {
                    
                } else {
                    return $phrase.$closeMark.$equal;
                }
            } 
        } 
    }

    return $phrase . $closeMark;
}
