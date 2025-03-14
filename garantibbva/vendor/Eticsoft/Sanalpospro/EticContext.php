<?php
namespace Eticsoft\Sanalpospro;

class EticContext
{
    public static function get($key)
    {   
        global $woocommerce;
        return $woocommerce->$key;
    }
}