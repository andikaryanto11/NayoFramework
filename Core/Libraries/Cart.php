<?php

namespace Core\Libraries;

use Core\Session;

class Cart
{

    public static function add($data)
    {
        $sescart = (array) Session::get(appKey_config() . "cart");
        if (empty($sescart)){
            $data['options']['total'] = $data['qty'] * $data['price'];
            $sescart[] = $data;
        } else {
            if (self::isValidToSum($data)) {
                $i = 0;
                foreach ($sescart as $sc) {
                    if ($data['id'] == $sc['id']) {
                        if (isset($data['options']['size']) && isset($sc['options']['size']) && $sc['options']['size'] == $data['options']['size']) {
                            $sescart[$i]["qty"] += $data['qty'];
                            $sescart[$i]['options']["total"] = $sescart[$i]["qty"] * $sescart[$i]["price"];
                            break;
                        }
                    }
                    $i++;
                }
            } else {
                $data['options']['total'] = $data['qty'] * $data['price'];
                $sescart[] = $data;
            }
        }
        Session::set(appKey_config() . "cart", $sescart);
    }

    private static function isValidToSum($cart)
    {
        $sescart = (array) Session::get(appKey_config() . "cart");
        foreach ($sescart as $sc) {
            if ($cart['id'] == $sc['id']) {
                if (isset($cart['options']['size']) && isset($sc['options']['size']) && $sc['options']['size'] == $cart['options']['size']) {
                    return true;
                }
            }
        }
    }

    public static function total()
    {
        $total = 0;
        $sescart = (array) Session::get(appKey_config() . "cart");
        foreach($sescart as $sc){
            $total += $sc['options']['total'];
        }
        return $total;
        
    }

    public function clear()
    {
        Session::unset(appKey_config() . "cart");
    }

    public function collect()
    {
        return Session::get(appKey_config() . "cart");
    }
}
