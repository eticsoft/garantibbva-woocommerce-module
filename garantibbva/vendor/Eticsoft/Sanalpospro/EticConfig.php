<?php
namespace Eticsoft\Sanalpospro;

class EticConfig
{
    public static function get($key)
    {
        return get_option($key);
    }

    public static function set($key, $value)
    {
        return update_option($key, $value);
    }

    public static function getWooValue($key) {
        switch ($key) {
            case 'store_name':
                return get_bloginfo('name');
            case 'store_description':
                return get_bloginfo('description');
            case 'store_url':
                return get_site_url();
            case 'store_email':
                return get_option('admin_email');
            case 'store_address':
                return get_option('woocommerce_store_address');
            case 'store_address_2':
                return get_option('woocommerce_store_address_2');
            case 'store_city':
                return get_option('woocommerce_store_city');
            case 'store_postcode':
                return get_option('woocommerce_store_postcode');
            case 'store_country':
                return get_option('woocommerce_default_country');
            case 'currency':
                return get_woocommerce_currency();
            case 'currency_symbol':
                return get_woocommerce_currency_symbol();
            case 'store_phone':
                $phone = get_option('woocommerce_store_phone');
                if (empty($phone)) {
                    $phone = get_option('options_phone'); 
                }
                if (empty($phone)) {
                    $phone = get_theme_mod('store_phone'); 
                }
                return $phone ? $phone : '';
            case 'store_language':
                return get_locale();
            default:
                return '';
        }
    }
}