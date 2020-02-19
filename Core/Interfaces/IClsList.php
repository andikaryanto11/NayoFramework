<?php
namespace Core\Interfaces;

interface IClsList{
    function add($object);
    function collections();
    function getCollectionType();
}