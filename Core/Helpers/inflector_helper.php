<?php
use ICanBoogie\Inflector;

function pluralize($string){
    $inflector = Inflector::get('en');
    return $inflector->pluralize($string);
}

function singularize($string){
    $inflector = Inflector::get('en');
    return $inflector->singularize($string);
}

function camelize($string){
    $inflector = Inflector::get('en');
    return $inflector->camelize($string);
}

function underscore($string){
    $inflector = Inflector::get('en');
    return $inflector->underscore($string);
}

function humanize($string){
    $inflector = Inflector::get('en');
    return $inflector->humanize($string);
}

function titleize($string){
    $inflector = Inflector::get('en');
    return $inflector->titleize($string);
}

function ordinal($string){
    $inflector = Inflector::get('en');
    return $inflector->ordinal($string);
}

function ordinalize($string){
    $inflector = Inflector::get('en');
    return $inflector->ordinalize($string);
}