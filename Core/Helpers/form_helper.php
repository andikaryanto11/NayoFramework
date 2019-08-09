<?php
function formOpen($action = "", $props = array(), $method= "POST"){
    $form = "";
    if(empty($props)){
        $form = "<form method = '{$method}' action='{$action}'> ";
        if($GLOBALS['config']['csrf_security']){
            $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
        }
    } else {

    }
    return $form;
}

function formOpenMultipart($action = "", $props = array(), $method= "POST"){
    $form = "";
    if(empty($props)){
        $form = "<form method = '{$method}' action='{$action}' enctype='multipart/form-data'>";
        if($GLOBALS['config']['csrf_security']){
            $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
        }
    } else {

    }
    return $form;
}

function formClose(){
    return "</form>";
}

function formInput($props = array()){
    $inputProp ="";
    if(!empty($props)){
        foreach ($props as $key => $val){
            if($val)
                $inputProp .= $key ." = '{$val}'";
            else 
                $inputProp .= " ".$key." ";
        }
    }

    return "<input {$inputProp}> ";

}

function formSelect($datas, $value, $name, $props = array()){
    $inputProp ="";
    if(!empty($props)){
        foreach ($props as $key => $val){
            if($val)
                $inputProp .= $key ." = '{$val}'";
            else 
                $inputProp .= " ".$key." ";
        }
    }

    $select = "<select {$inputProp}>";
    $option = "";

    foreach ($datas as $data )
        $option .= "<option value = {$data->$value}>{$data->$name} </option> ";
    
    $select .= $option. "</select>";
    return $select;

}

function formTextArea($text = "", $props = array()){
    $textAreaProp ="";
    if(!empty($props)){
        foreach ($props as $key => $val){
            if($val)
                $textAreaProp .= $key ." = '{$val}'";
            else 
                $textAreaProp .= " ".$key;
        }
    }

    return "<textarea {$textAreaProp}>{$text}</textarea>";
}

function formLink($text, $props = array()){
    $linkProp ="";
    if(!empty($props)){
        foreach ($props as $key => $val){
            if($val)
                $linkProp .= $key ." = '{$val}'";
            else 
                $linkProp .= " ".$key;
        }
    }

    return "<a {$linkProp} >{$text}</a>";
}

function formLabel($text, $props = array()){
    $labelProp ="";
    if(!empty($props)){
        foreach ($props as $key => $val){
            if($val)
                $labelProp .= $key ." = '{$val}'";
            else 
                $labelProp .= " ".$key;
        }
    }

    return "<label {$labelProp}>{$text}</label>";
}