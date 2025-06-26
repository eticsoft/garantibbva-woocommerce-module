<?php
namespace Eticsoft\Sanalpospro;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class EticContext
{
    public static function get($key)
    {   
        global $woocommerce;
        return $woocommerce->$key;
    }
}