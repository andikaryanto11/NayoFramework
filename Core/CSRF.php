<?php
namespace Core;

class CSRF {
    static protected $length = 256;
    static protected $tokenName = "CsrfNayo";

    public static function getCsrfHash(){
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, static::$length); 
    }

    public static function getCsrfTokenName(){
        return static::$tokenName;
    }

}