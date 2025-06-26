<?php
namespace Eticsoft\Sanalpospro;

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class EticTools {
    /**
     * Get POST value with fallback
     */ 
    public static function postVal($key, $default = null) {
        return isset($_POST[$key]) ? sanitize_text_field($_POST[$key]) : $default;
    }

    /**
     * Get GET value with fallback
     */
    public static function getVal($key, $default = null) {
        return isset($_GET[$key]) ? sanitize_text_field($_GET[$key]) : $default;
    }
}