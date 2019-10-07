<?php

use Core\Session;

function formOpen($action = "", $props = array(), $method = "POST")
{
    $form = "";
    if (empty($props)) {
        $form = "<form method = '{$method}' action='{$action}'> ";
        if ($GLOBALS['config']['csrf_security']) {
            $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
        }
    } else { 
        $property = "";
        foreach($props as $key => $prop){
            $property.= "{$key} = '{$prop}'";
        }
        $form = "<form method = '{$method}' action='{$action}' {$property}> ";
        if ($GLOBALS['config']['csrf_security']) {
            $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
        }
    }
    return $form;
}

function formOpenMultipart($action = "", $props = array(), $method = "POST")
{
    $form = "";
    if (empty($props)) {
        $form = "<form method = '{$method}' action='{$action}' enctype='multipart/form-data'>";
        if ($GLOBALS['config']['csrf_security']) {
            $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
        }
    } else {
        $property = "";
        foreach($props as $key => $prop){
            $property.= "{$key} = '{$prop}'";
        }
        $form = "<form method = '{$method}' action='{$action}' {$property}> ";
        if ($GLOBALS['config']['csrf_security']) {
            $form .= "<input hidden name='{$_SESSION['csrfName']}' value = '{$_SESSION['csrfToken']}'>";
        }
     }
    return $form;
}

function formClose()
{
    return "</form>";
}

function formInput($props = array())
{

    $session = Session::getInstance();
    $inputProp = "";
    $sesdata = null;
    if ($session->get('data')) {
        $sesdata = $session->get('data');
    }

    if (!empty($props)) {
        $datavalue = null;
        if (key_exists('name', $props) && $sesdata) {
            if (key_exists($props['name'], $sesdata)) {
                $datavalue = $sesdata[$props['name']];
            }
        }

        foreach ($props as $key => $val) {
            if ($key == "value" && $datavalue) {
                $val = htmlspecialchars($datavalue, ENT_QUOTES);
            } else {
                $val = htmlspecialchars($val, ENT_QUOTES);
            }
            if ($val) {
                $inputProp .= $key . " = '{$val}'";
            } else {
                $inputProp .= " " . $key . " ";
            }
        }
    }

    return "<input {$inputProp}> ";
}

function formSelect($datas, $value, $name, $props = array())
{
    $inputProp = "";
    if (!empty($props)) {
        foreach ($props as $key => $val) {
            if ($val)
                $inputProp .= $key . " = '{$val}'";
            else
                $inputProp .= " " . $key . " ";
        }
    }


    $select = "<select {$inputProp}>";
    $option = "";

    // if (is_array($datas)) {
    //     foreach ($datas as $data)
    //         $option .= "<option value = {$data[$value]}>{$data[$name]} </option> ";
    // } else {

    foreach ($datas as $data)
        $option .= "<option value = {$data->$value}>{$data->$name} </option> ";
    // }

    $select .= $option . "</select>";
    return $select;
}

function formTextArea($text = "", $props = array())
{
    $session = Session::getInstance();
    $sesdata = null;
    $value = "";
    
    if ($session->get('data')) {
        $sesdata = $session->get('data');
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
