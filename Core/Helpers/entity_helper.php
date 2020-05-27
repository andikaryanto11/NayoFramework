<?php

function table_entity($entity)
{
    $tab = pluralize($entity);
    
    $split = explode("_", $tab);
    return $split[0]."_".lcfirst($split[1]);
    // return $tab;
    
}

function entity_entity($table)
{
    $word = titleize(singularize($table));
    $split = explode(" ", $word);
    return implode("_", $split);
}