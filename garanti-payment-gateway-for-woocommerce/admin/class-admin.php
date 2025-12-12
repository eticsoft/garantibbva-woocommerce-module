<?php
if (!defined('ABSPATH')) {
    exit;
}
include_once GBBVA_PLUGIN_DIR . 'vendor/include.php';
use Eticsoft\Sanalpospro\EticConfig;

class GBBVA_Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
       
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts($hook) {
        if ('woocommerce_page_gbbva_admin' !== $hook) {
            return;
        }

        // Enqueue admin CSS
        wp_enqueue_style(
            'gbbva-admin-style',
            GBBVA_PLUGIN_URL . 'admin/css/admin.css',
            array(),
            GBBVA_VERSION
        );
        
        // Enqueue Paythor dashboard style
        wp_enqueue_style(
            'paythor-dashboard-style', 
            GBBVA_PLUGIN_URL . 'admin/css/index.css', 
            array(), 
            GBBVA_VERSION
        );

        // Enqueue admin settings script
        wp_register_script('gbbva-admin-settings', 
            GBBVA_PLUGIN_URL . 'admin/js/admin-settings.js', 
            array('jquery'),
            GBBVA_VERSION, 
            true
        ); 

        // Prepare settings
        $settings = [
            'order_status' => EticConfig::get('GARANTIBBVA_ORDER_STATUS') ?: 'processing',
            'currency_convert' => EticConfig::get('GARANTIBBVA_CURRENCY_CONVERT') ?: 'no',
            'showInstallmentsTabs' => EticConfig::get('GARANTIBBVA_SHOWINSTALLMENTSTABS') ?: 'no',
            'paymentPageTheme' => EticConfig::get('GARANTIBBVA_PAYMENTPAGETHEME') ?: 'classic',
            'installments' => json_decode(EticConfig::get('GARANTIBBVA_INSTALLMENTS'), true) ?: [],
            'public_key' => EticConfig::get('GARANTIBBVA_PUBLIC_KEY'),
            'secret_key' => EticConfig::get('GARANTIBBVA_SECRET_KEY')
        ];

        // Add inline script with settings
        wp_add_inline_script('gbbva-admin-settings', 'window.generalSettings = ' . wp_json_encode([
            'order_status' => [
                'default_value' => $settings['order_status'] ?? 'processing',
                'options' => $this->get_order_statuses()
            ],
            'currency_convert' => [
                'default_value' => $settings['currency_convert'] ?? 'no',
                'options' => [
                    'yes' => __('Yes', 'garanti-payment-module'),
                    'no' => __('No', 'garanti-payment-module')
                ]
            ],
            'showInstallmentsTabs' => [
                'default_value' => $settings['showInstallmentsTabs'] ?? 'no',
                'options' => [
                    'yes' => __('Yes', 'garanti-payment-module'),
                    'no' => __('No', 'garanti-payment-module')
                ]
            ],
            'paymentPageTheme' => [
                'default_value' => $settings['paymentPageTheme'] ?? 'modern',
                'options' => [
                    'classic' => __('Classic', 'garanti-payment-module'),
                    'modern' => __('Modern', 'garanti-payment-module')
                ]
            ]
        ]) . ';', 'before');

        wp_enqueue_script('gbbva-admin-settings');

        // Add API nonce and URLs
        $xfvv = wp_create_nonce('gbbva_internal_api_request');
        $iapi_base_url = 'admin-ajax.php?action=gbbva_internal_api_request';

        wp_add_inline_script('gbbva-admin-settings', 'window.target_url = "' . esc_js($iapi_base_url) . '";', 'before');
        wp_add_inline_script('gbbva-admin-settings', 'window.xfvv = "' . esc_js($xfvv) . '";', 'before');
        wp_add_inline_script('gbbva-admin-settings', 'window.store_url = "' . esc_js(get_site_url()) . '";', 'before');
         wp_add_inline_script('gbbva-admin-settings', 'window.style_url = "' . esc_js(GBBVA_PLUGIN_URL . 'admin/css/index.css') . '";', 'before');
        // Enqueue Paythor dashboard script
        wp_register_script(
            'paythor-dashboard', 
             GBBVA_PLUGIN_URL . 'admin/js/index.js', 
            array('gbbva-admin-settings'), 
            GBBVA_VERSION, 
            true
        );

        // Add module type to script tag
        add_filter('script_loader_tag', function($tag, $handle, $src) {
            if ('paythor-dashboard' === $handle) {
                return str_replace('<script ', '<script type="module" defer ', $tag);
            }
            return $tag;
        }, 10, 3);

        wp_enqueue_script('paythor-dashboard','',[],'false',array('strategy'=>'defer','in_footer'=>true));

       
        wp_add_inline_script('paythor-dashboard', 
            'document.addEventListener("DOMContentLoaded", function () {
                const wpBodyContent = document.querySelector("#wpbody-content");
                if (wpBodyContent) {
                    wpBodyContent.style.padding = "0";
                }
                const wpFooterDisplayHidden = document.querySelector("#wpfooter");
                if (wpFooterDisplayHidden) {
                   wpFooterDisplayHidden.style.display = "none";
                }
            });', 
            'after'
        );
    }

    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('GarantiBBVA Settings', 'garanti-payment-module'),  
            __('GarantiBBVA', 'garanti-payment-module'),           
            'manage_woocommerce',                      
            'gbbva_admin',                           
            array($this, 'render_admin_page')
        );
    }

    private function get_order_statuses() {
        $statuses = wc_get_order_statuses();
        $formatted = array();
        foreach ($statuses as $key => $label) {
            $key = str_replace('wc-', '', $key);
            $formatted[$key] = $label;
        }
        return $formatted;
    }

    public static function set_default_settings() {
        // Set default settings if not already set
        if (!EticConfig::get('GARANTIBBVA_ORDER_STATUS')) {
            EticConfig::set('GARANTIBBVA_ORDER_STATUS', 'processing');
        }
        if (!EticConfig::get('GARANTIBBVA_CURRENCY_CONVERT')) {
            EticConfig::set('GARANTIBBVA_CURRENCY_CONVERT', 'no');
        }
        if (!EticConfig::get('GARANTIBBVA_SHOWINSTALLMENTSTABS')) {
            EticConfig::set('GARANTIBBVA_SHOWINSTALLMENTSTABS', 'no');
        }
        if (!EticConfig::get('GARANTIBBVA_PAYMENTPAGETHEME')) {
            EticConfig::set('GARANTIBBVA_PAYMENTPAGETHEME', 'classic');
        }
    }

    public function render_admin_page() {
        // Prepare variables for the template
        $token = EticConfig::get('GARANTIBBVA_PUBLIC_KEY') ?: '';
        $site_url = get_site_url();
        
        // Include the template file
        include_once GBBVA_PLUGIN_DIR . 'admin/views/admin-page.php';
    }
}

