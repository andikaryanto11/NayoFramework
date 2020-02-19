<?php

function money_currency($number){
    return number_format($number, 2, ",", ".");
}