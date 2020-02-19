<?php

use Core\Session;

function formOpen($action = "", $props = array(), $method = "POST")
{
    $form = "";
    $property = "";
    if (!empty($props)) {
        foreach ($props as $key => $prop) {
            $property .= "{$key} = '{$prop}'";
        }
    }

    $act="";
    if(!empty($action)){
        $act = "action='{$action}'";
    }
    
    $form = "<form method = '{$method}' {$act} {$property}> ";
    if ($GLOBALS['config']['csrf_security']) {
        $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
    }
    return $form;
}

function formOpenMultipart($action = "", $props = array(), $method = "POST")
{
    $form = "";
    $property = "";
    if (!empty($props)) {
        foreach ($props as $key => $prop) {
            $property .= "{$key} = '{$prop}'";
        }
    }
    
    $act="";
    if(!empty($action)){
        $act = "action='{$action}'";
    }

    $form = "<form method = '{$method}' {$act} {$property}  enctype='multipart/form-data'> ";
    if ($GLOBALS['config']['csrf_security']) {
        $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
    }
    return $form;
}

function formClose()
{
    return "</form>";
}

function formInput($props = array())
{

    $inputProp = "";
    $sesdata = null;
    $datavalue = null;
    $checked = "";
    if (\Core\Session::get('data')) {
        $sesdata = \Core\Session::get('data');
    }

    if (!empty($props)) {
        if (key_exists('name', $props) && $sesdata) {
            if (key_exists($props['name'], $sesdata)) {
                $datavalue = $sesdata[$props['name']];
            }
        }

        if (key_exists('type', $props)){
            if($props['type'] == "checkbox"){
                if(isset($props['value'])){
                    $checked = "checked=''";
                }
            }
        }

        foreach ($props as $key => $val) {
            $newvalue = null;
            if ($key == "value" && !is_null($datavalue)) {
               
                $newvalue = htmlspecialchars($datavalue, ENT_QUOTES);
            } else {
                $newvalue = htmlspecialchars($val, ENT_QUOTES);
            }
            if (!empty($newvalue)) {
                $inputProp .= $key . " = '{$newvalue}'";
            } else {
                $inputProp .= " " . $key . " ";
            }
        }
    }

    return "<input $checked {$inputProp}> ";
}

function formSelect($datas, $value, $name, $props = array())
{
    $inputProp = "";
    if (!empty($props)) {
        foreach ($props as $key => $val) {
            if($key != 'value')
                if ($val)
                    $inputProp .= $key . " = '{$val}'";
                else
                    $inputProp .= " " . $key . " ";
        }
    }


    $select = "<select {$inputProp}>";
    $selected = "";
    $option = "";

    // if (is_array($datas)) {
    //     foreach ($datas as $data)
    //         $option .= "<option value = {$data[$value]}>{$data[$name]} </option> ";
    // } else {
    
    foreach ($datas as $data)
        if(isset($props['value'])){
            if($props['value'] == $data->$value){

                $option .= "<option value = {$data->$value} selected>{$data->$name} </option> ";
            }
            else 
                $option .= "<option value = {$data->$value}>{$data->$name} </option> ";
        }
        else 
            $option .= "<option value = {$data->$value}>{$data->$name} </option> ";
    // }

    $select .= $option . "</select>";
    return $select;
}

function formTextArea($text = "", $props = array())
{
    $sesdata = null;
    $value = "";

    if (\Core\Session::get('data')) {
        $sesdata = \Core\Session::get('data');
    }

    if (key_exists('name', $props) && $sesdata) {
        if (key_exists($props['name'], $sesdata)) {
            $value = $sesdata[$props['name']];
        }
    } else {
        $value = $text;
    }

    $textAreaProp = "";
    if (!empty($props)) {
        foreach ($props as $key => $val) {
            if ($val)
                $textAreaProp .= $key . " = '{$val}'";
            else
                $textAreaProp .= " " . $key;
        }
    }

    return "<textarea {$textAreaProp}>{$value}</textarea>";
}

function formLink($text, $props = array())
{
    $linkProp = "";
    if (!empty($props)) {
        foreach ($props as $key => $val) {
            if ($val)
                $linkProp .= $key . " = '{$val}'";
            else
                $linkProp .= " " . $key;
        }
    }

    return "<a {$linkProp} >{$text}</a>";
}

function formLabel($text, $props = array())
{
    $labelProp = "";
    if (!empty($props)) {
        foreach ($props as $key => $val) {
            if ($val)
                $labelProp .= $key . " = '{$val}'";
            else
                $labelProp .= " " . $key;
        }
    }

    return "<label {$labelProp}>{$text}</label>";
}
