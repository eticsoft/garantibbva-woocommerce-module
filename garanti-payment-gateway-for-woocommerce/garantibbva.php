<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/* ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); */

require_once __DIR__ . '/vendor/include.php';

use Eticsoft\Sanalpospro\EticConfig;

/*
 * Plugin Name: GarantiBBVA Payment Gateway
 * Plugin URI: https://garantibbva.com
 * Description: GarantiBBVA payment gateway for WooCommerce
 * Version: 2.1.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: EticSoft R&D Lab.
 * Author URI: https://eticsoft.com
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: garanti-payment-gateway-for-woocommerce
 * Domain Path: /languages
 */

// Define constants
if (!defined('GBBVA_VERSION')) {
    define('GBBVA_VERSION', '2.1.0');
}
if (!defined('GBBVA_PLUGIN_URL')) {
    define('GBBVA_PLUGIN_URL', plugin_dir_url(__FILE__));
}
if (!defined('GBBVA_PLUGIN_DIR')) {
    define('GBBVA_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
define('GBBVA_PLUGIN_FILE', __FILE__);

// Hook registration
register_activation_hook(__FILE__, 'gbbva_activate_plugin');
add_action('plugins_loaded', 'gbbva_setup_gateway_class');
add_action('plugins_loaded', 'gbbva_setup_admin_page');
add_action('plugins_loaded', 'gbbva_check_theme_compatibility');
add_action('wp_footer', 'gbbva_add_payment_iframe_script');
add_action('wp_ajax_gbbva_internal_api_request', 'gbbva_internal_api_request');
add_action('wp_ajax_nopriv_gbbva_internal_api_request', 'gbbva_internal_api_request');
add_action('wp_enqueue_scripts', 'gbbva_enqueue_styles');
add_action('admin_enqueue_scripts', 'gbbva_enqueue_admin_assets');


/**
 * Enqueue admin assets
 */
function gbbva_enqueue_admin_assets() {
    wp_enqueue_style('gbbva-admin-popup', GBBVA_PLUGIN_URL . 'assets/css/admin-popup.css', array(), GBBVA_VERSION);
    wp_enqueue_script('gbbva-admin-popup', GBBVA_PLUGIN_URL . 'assets/js/admin-popup.js', array('jquery'), GBBVA_VERSION, true);
}

/**
 * Check theme compatibility
 * Displays admin notice if WooCommerce block checkout is enabled
 */
function gbbva_check_theme_compatibility()
{

    if (!is_admin()) {
        return;
    }


    $using_wc_blocks = false;


    if (class_exists('WC_Blocks_Utils') && method_exists('WC_Blocks_Utils', 'is_block_checkout_enabled')) {
        $using_wc_blocks = \WC_Blocks_Utils::is_block_checkout_enabled();
    }


    if (!$using_wc_blocks && function_exists('wc_get_page_id')) {
        $checkout_page_id = wc_get_page_id('checkout');
        if ($checkout_page_id > 0) {
            $checkout_page = get_post($checkout_page_id);
            if ($checkout_page && has_block('woocommerce/checkout', $checkout_page->post_content)) {
                $using_wc_blocks = true;
            }
        }
    }


    if ($using_wc_blocks) {
        add_action('admin_notices', 'gbbva_block_checkout_admin_notice');
    }
}

/**
 * Display admin notice for WooCommerce block checkout incompatibility
 */
function gbbva_block_checkout_admin_notice()
{
   // Ensure the popup HTML is added to the footer
   add_action('admin_footer', 'gbbva_popup_html');
   ?>
       <div class="notice notice-error gbbva-notice" style="display: flex; align-items: center; padding: 15px 20px;">
           <div style="margin-right: 15px;">
               <?php
               // Using proper WordPress way to display an image
               $image_attributes = [
                   'src' => esc_url(WC()->plugin_url() . '/assets/images/icons/info.svg'),
                   'alt' => esc_attr__('Warning', 'garanti-payment-gateway-for-woocommerce'),
                   'width' => 48,
                   'height' => 48,
               ];
               
               echo '<img';
               foreach ($image_attributes as $name => $value) {
                   echo ' ' . esc_attr($name) . '="' . esc_attr($value) . '"';
               }
               echo '>';
               ?>
           </div>
           <div>
               <p style="font-size: 16px; margin: 0; line-height: 1.5;"><strong><?php esc_html_e('GarantiBBVA Payment Gateway Warning', 'garanti-payment-gateway-for-woocommerce'); ?></strong>: <?php esc_html_e('You need to switch to Classic Checkout view!', 'garanti-payment-gateway-for-woocommerce'); ?> 
                   <button id="gbbva-show-instructions" class="button button-primary" style="margin-left: 15px; font-size: 14px; padding: 5px 15px;"><?php esc_html_e('How to do it?', 'garanti-payment-gateway-for-woocommerce'); ?></button>
               </p>
           </div>
       </div>
   <?php
}

/**
 * Add popup HTML to admin footer
 */
function gbbva_popup_html() {
    ?>
    <div class="gbbva-popup-overlay" id="gbbva-popup">
        <div class="gbbva-popup-content">
            <a href="#" class="gbbva-popup-close">&times;</a>
            <div class="gbbva-popup-title"><?php esc_html_e('GarantiBBVA Payment Gateway Compatibility Notice', 'garanti-payment-gateway-for-woocommerce'); ?></div>
            <p style="font-size: 15px; line-height: 1.6;"><?php esc_html_e('After the WooCommerce infrastructure update, the block-based payment option view is not currently available in our plugin. This issue is in our planning and will be completed by our team as soon as possible.', 'garanti-payment-gateway-for-woocommerce'); ?></p>
            <p style="font-size: 15px; line-height: 1.6;"><strong><?php esc_html_e('The classic payment view can be activated with the following steps:', 'garanti-payment-gateway-for-woocommerce'); ?></strong></p>
            <ol style="font-size: 15px; line-height: 1.8;">
                <li><?php esc_html_e('Back up your site just in case.', 'garanti-payment-gateway-for-woocommerce'); ?></li>
                <li><?php esc_html_e('Go to WordPress admin panel > Pages > Checkout Page.', 'garanti-payment-gateway-for-woocommerce'); ?></li>
                <li><?php esc_html_e('After clicking on Payment options, click on the Block button in the right menu.', 'garanti-payment-gateway-for-woocommerce'); ?></li>
                <li><?php esc_html_e('Click on the Switch to classic checkout button in this area and save the changes with the Save button.', 'garanti-payment-gateway-for-woocommerce'); ?></li>
            </ol>
            <p style="font-size: 15px; line-height: 1.6;"><?php esc_html_e('After these steps, the Classic payment view will be active and you can use our plugin.', 'garanti-payment-gateway-for-woocommerce'); ?></p>
        </div>
    </div>
    <?php
    }
    
/**
 * Plugin activation function
 * Checks requirements and sets up default settings
 */
function gbbva_activate_plugin()
{

    if (version_compare(PHP_VERSION, '7.4', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html(sprintf(
                /* translators: 1: Required PHP version, 2: Current PHP version */
                __('GarantiBBVA Payment Gateway requires PHP version %1$s or higher. You are running version %2$s. Please upgrade PHP and try again.', 'garanti-payment-gateway-for-woocommerce'),
                '7.4',
                PHP_VERSION
            )),
            esc_html(__('Plugin Activation Error', 'garanti-payment-gateway-for-woocommerce')),
            array('back_link' => true)
        );
    }


    if (version_compare($GLOBALS['wp_version'], '5.8', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html(sprintf(
                /* translators: 1: Required WordPress version, 2: Current WordPress version */
                __('GarantiBBVA Payment Gateway requires WordPress version %1$s or higher. You are running version %2$s. Please upgrade WordPress and try again.', 'garanti-payment-gateway-for-woocommerce'),
                '5.8',
                esc_html($GLOBALS['wp_version'])
            )),
            esc_html(__('Plugin Activation Error', 'garanti-payment-gateway-for-woocommerce')),
            array('back_link' => true)
        );
    }


    if (!class_exists('WooCommerce')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            esc_html(__('GarantiBBVA Payment Gateway requires WooCommerce to be installed and activated.', 'garanti-payment-gateway-for-woocommerce')),
            esc_html(__('Plugin Activation Error', 'garanti-payment-gateway-for-woocommerce')),
            array('back_link' => true)
        );
    }

    // Set default settings
    $default_settings = array(
        'GARANTIBBVA_ORDER_STATUS' => 'processing',
        'GARANTIBBVA_CURRENCY_CONVERT' => 'no',
        'GARANTIBBVA_SHOWINSTALLMENTSTABS' => 'no',
        'GARANTIBBVA_PAYMENTPAGETHEME' => 'classic',
        'GARANTIBBVA_INSTALLMENTS' => json_encode([]),
        'GARANTIBBVA_VERSION' => GBBVA_VERSION
    );

    foreach ($default_settings as $key => $value) {
        if (false === get_option($key)) {
            update_option($key, $value);
        }
    }

    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Setup admin page
 */
function gbbva_setup_admin_page()
{
    if (is_admin()) {
        require_once GBBVA_PLUGIN_DIR . 'admin/class-admin.php';
        new GBBVA_Admin();
    }
}

/**
 * Setup payment gateway class
 */
function gbbva_setup_gateway_class()
{
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    }

    /**
     * GarantiBBVA Payment Gateway Class
     */
    class GBBVA_Payment_Gateway extends WC_Payment_Gateway
    {
        /**
         * Constructor for the gateway
         */
        public function __construct()
        {
            $this->id = 'garantibbva';
            $this->icon = GBBVA_PLUGIN_URL . 'assets/images/garantibbva-logo.png';
            $this->has_fields = false;
            $this->method_title = 'GarantiBBVA';
            $this->method_description = __('GarantiBBVA Payment Gateway for WooCommerce', 'garanti-payment-gateway-for-woocommerce');
            $this->supports = array('products');

            $this->init_form_fields();
            $this->init_settings();

            $this->title = __('Pay via Card (GarantiBBVA)', 'garanti-payment-gateway-for-woocommerce');
            $this->description = __('Payment by your credit/debit card.', 'garanti-payment-gateway-for-woocommerce');

            // Actions and filters
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
            add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
            add_filter('wp_kses_allowed_html', array($this, 'allow_iframe_in_html'), 10, 2);
            add_filter('woocommerce_gateway_' . $this->id . '_settings_args', array($this, 'remove_wpautop'));

            if (is_admin()) {
                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
                add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'show_payment_warning'), 10, 1);
            }
        }


        /**
         * Initialize form fields for the gateway settings
         */
        public function init_form_fields()
        {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable/Disable', 'garanti-payment-gateway-for-woocommerce'),
                    'type' => 'checkbox',
                    'label' => __('Enable/Disable GarantiBBVA payment gateway', 'garanti-payment-gateway-for-woocommerce'),
                    'default' => 'yes',
                    'description' => __('Enable/Disable GarantiBBVA payment gateway', 'garanti-payment-gateway-for-woocommerce'),
                    'desc_tip' => true,
                ),
                'panel_button' => array(
                    'title' => '',
                    'type' => 'panel_button',
                    'description' => '',
                    'desc_tip' => false,
                ),
            );
        }

        /**
         * Check if gateway is available for use
         */
        public function is_available()
        {

            if ('no' === $this->get_option('enabled')) {
                return false;
            }


            if (!class_exists('WC_Payment_Gateway')) {
                return false;
            }

            return true;
        }

        /**
         * Generate panel button HTML
         */
        public function generate_panel_button_html($key, $data)
        {
            return '<tr><td colspan="2" style="padding-left: 0;">
                <a href="' . esc_url(admin_url('admin.php?page=gbbva_admin')) . '" 
                   class="button button-primary">
                    ' . esc_html__('Click to Access GarantiBBVA Panel', 'garanti-payment-gateway-for-woocommerce') . '
                </a>
            </td></tr>';
        }

     
        /**
         * Payment fields displayed on checkout
         */
        public function payment_fields()
        {
            $supported_cards = array('visa', 'mastercard', 'amex', 'troy');

            gbbva_get_template('checkout/payment-form.php', array(
                'description' => $this->description,
                'supported_cards' => $supported_cards
            ));
        }

        /**
         * Process payment
         */
        public function process_payment($order_id)
        {
            $order = wc_get_order($order_id);
            $pay_for_order = isset($_GET['pay_for_order']) && sanitize_text_field(wp_unslash($_GET['pay_for_order']));
            $xfvv = wp_create_nonce('gbbva_internal_api_request');
            $receipt_nonce = wp_create_nonce('gbbva_payment_confirmation');
            try {
                $api = new \Eticsoft\Sanalpospro\InternalApi();
                $data = [
                    'order_id' => $order_id,
                    'receipt_nonce' => $receipt_nonce
                ];
                $res = ($api->run('CreatePaymentLink', $data))->getResponse();

                if ($res['status'] !== 'success') {
                    throw new Exception(sprintf(
                        '<div>%s: %s</div> <div>%s</div>',
                        esc_html__('Error Code', 'garanti-payment-gateway-for-woocommerce'),
                        esc_html($res['status']),
                        esc_html($res['message'])
                    ));
                }
                $order_confirmation_url = add_query_arg(
                    array(
                        'order_id' => $order_id,
                        'key' => $order->get_order_key(),
                        '_wpnonce' => $receipt_nonce
                    ),
                    $order->get_checkout_payment_url(true)
                );

                if ($pay_for_order) {
                    return array(
                        'result' => 'success',
                        'redirect' => $order->get_checkout_payment_url(true)
                    );
                }


                ob_start();
                gbbva_get_template('checkout/payment-iframe.php', array(
                    'payment_link' => $res['data']['payment_link']
                ));
                $iframe_html = ob_get_clean();

                return array(
                    'result' => 'success',
                    'messages' => 'Payment link created successfully',
                    'iframe_html' => $iframe_html,
                    'redirect_url' => $order_confirmation_url
                ); 
            } catch (Exception $e) {
                wc_add_notice($e->getMessage(), 'error');
                return array(
                    'result' => 'failure',
                    'messages' => $e->getMessage()
                );
            }
        }

        /**
         * Receipt page
         */

        public function receipt_page($order_id)
        {
         
            // Verify nonce to prevent CSRF attacks
            if (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'gbbva_payment_confirmation')) {
                wp_die(esc_html__('Security check failed. Please try again.', 'garanti-payment-gateway-for-woocommerce'));
            }

            // Verify user has permission to view this order
            $order = wc_get_order(absint($order_id));
            if (!$order) {
                wp_die(esc_html__('Invalid order.', 'garanti-payment-gateway-for-woocommerce'));
            }

            // Check if user has permission to view this order
            if (!current_user_can('manage_woocommerce') && $order->get_customer_id() !== get_current_user_id()) {
                wp_die(esc_html__('You do not have permission to view this order.', 'garanti-payment-gateway-for-woocommerce'));
            }

            // Sanitize and validate input parameters
            $p_id = isset($_GET['p_id']) ? sanitize_text_field(wp_unslash($_GET['p_id'])) : null;
            $id = empty($id) ? $order_id : $id;

            if (!$id) {
                wp_die(esc_html__('Invalid payment ID.', 'garanti-payment-gateway-for-woocommerce'));
            }

            try {
                $api = new \Eticsoft\Sanalpospro\InternalApi();

                $data = [
                    'process_token' => $p_id,
                    'order_id' => $id,
                ];

                $apiReq = $api->getInstance()->run('confirmOrder', $data);
                $response = $apiReq->getResponse();


                if ($response['status'] !== 'success') {
                    $order->update_status('failed', __('Payment failed', 'garanti-payment-gateway-for-woocommerce'));
                    wc_add_notice($response['message'], 'error');
                    wp_redirect(wc_get_checkout_url());
                    exit;
                }

                $order->update_status(EticConfig::get('GARANTIBBVA_ORDER_STATUS'), __('Processing GarantiBBVA payment', 'garanti-payment-gateway-for-woocommerce'));
                $order->add_order_note(
                    sprintf(
                        /* translators: %s: Payment gateway name */
                        __('Payment completed successfully via %s.', 'garanti-payment-gateway-for-woocommerce'),
                        $response['data']['gateway']  ?? 'garantibbva'
                    )
                );
                $amount = $response['data']['amount'] ?? 0;
                $original_total = $order->get_total();
                $transaction_amount = $amount;
                $tax_amount = $transaction_amount - $original_total;
                $fee = new WC_Order_Item_Fee();
                $fee->set_name(__('Commission Fee', 'garanti-payment-gateway-for-woocommerce'));
                $fee->set_amount($tax_amount);
                $fee->set_total($tax_amount);
                $fee->set_tax_class('');
                $fee->set_tax_status('none');
                $order->add_item($fee);
                $order->calculate_totals();
                $order->save();
                WC()->cart->empty_cart();
                $checkOutUrl = $order->get_checkout_order_received_url();
                return wp_redirect($checkOutUrl);
            } catch (Exception $e) {
                $order->update_status('failed', $e->getMessage());
                wc_add_notice($e->getMessage(), 'error');
                wp_redirect(wc_get_checkout_url());
                exit;
            }
        }

        /**
         * Show payment warning in admin
         */
        public function show_payment_warning($order) {
            // Static flag to prevent duplicate warnings
            static $warning_shown = [];
            
            // Create a unique ID for this order
            $order_id = $order->get_id();
            
            // Check if warning already shown for this order
            if (isset($warning_shown[$order_id])) {
                return;
            }
            
            if ($order->get_payment_method() === 'garantibbva') {
                // Only show warning for orders with completed payment status
                $completed_statuses = array(
                    EticConfig::get('GARANTIBBVA_ORDER_STATUS'),
                    'completed',
                    'processing'
                );
                
                $order_status = $order->get_status();
                
                if (in_array($order_status, $completed_statuses)) {
                    echo '<div class="notice notice-warning gbbva-warning" style="padding: 10px; margin: 10px 0;">
                        <h4>' . esc_html__('GarantiBBVA Payment Warning', 'garanti-payment-gateway-for-woocommerce') . '</h4>
                        <p>' . esc_html__('Payment was processed through GarantiBBVA', 'garanti-payment-gateway-for-woocommerce') . '</p>
                        <p>' . esc_html__('Please check the payment status and verify with your bank/payment institution.', 'garanti-payment-gateway-for-woocommerce') . '</p>
                    </div>';
                    
                    // Mark this order as having shown the warning
                    $warning_shown[$order_id] = true;
                }
            }
        }

       /*  public function show_payment_warning($order)
        {
            if ($order->get_payment_method() === 'garantibbva') {
                echo '<div class="notice notice-warning gbbva-warning" style="padding: 10px; margin: 10px 0;">
                    <h4>' . esc_html__('GarantiBBVA Payment Warning', 'garanti-payment-gateway-for-woocommerce') . '</h4>
                    <p>' . esc_html__('Payment was processed through GarantiBBVA', 'garanti-payment-gateway-for-woocommerce') . '</p>
                    <p>' . esc_html__('Please check the payment status and verify with your bank/payment institution.', 'garanti-payment-gateway-for-woocommerce') . '</p>
                </div>';
            }
        } */

        /**
         * Allow iframe in HTML
         */
        public function allow_iframe_in_html($tags, $context)
        {
            if ('admin' === $context) {
                $tags['a'] = array(
                    'href' => true,
                    'target' => true,
                );
            } else {
                $tags['iframe'] = array(
                    'src' => true,
                    'width' => true,
                    'height' => true,
                    'frameborder' => true,
                    'allowfullscreen' => true,
                    'style' => true,
                    'class' => 'gbbva-card-image',
                );
            }
            return $tags;
        }

        /**
         * Remove wpautop
         */
        public function remove_wpautop($settings)
        {
            remove_filter('woocommerce_payment_gateway_form_fields_' . $this->id, 'wpautop');
            return $settings;
        }

        /**
         * Enqueue scripts
         */
        public function enqueue_scripts()
        {


            $nonce = wp_create_nonce('gbbva_internal_api_request');


            wp_localize_script('jquery', 'gbbva_params', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'iapi_xfvv' => $nonce
            ));

            wp_enqueue_style('gbbva-payment-style', plugins_url('assets/css/payment.css', __FILE__), array(), GBBVA_VERSION);
        }
    }
}


add_filter('woocommerce_product_tabs', 'gbbva_add_installment_tab');

/**
 * Add installment tab to product page
 */
function gbbva_add_installment_tab($tabs)
{
    $showInstallmentsTabs = EticConfig::get('GARANTIBBVA_SHOWINSTALLMENTSTABS');
    if ($showInstallmentsTabs === 'yes') {
        $tabs['installment_tab'] = array(
            'title'    => __('Installment Options', 'garanti-payment-gateway-for-woocommerce'),
            'priority' => 50,
            'callback' => 'gbbva_installment_tab_content'
        );
    }
    return $tabs;
}

/**
 * Display installment tab content
 */
function gbbva_installment_tab_content()
{
    global $product;
    if (!$product) return;

    if (EticConfig::get('GARANTIBBVA_SHOWINSTALLMENTSTABS') === 'yes') {
        $theme = EticConfig::get('GARANTIBBVA_PAYMENTPAGETHEME');
        $theme = (!$theme || !in_array($theme, ['classic', 'modern'])) ? 'classic' : $theme;

        $settings = [
            'theme' => $theme,
            'price' => $product->get_price(),
            'installments' => EticConfig::get('GARANTIBBVA_INSTALLMENTS')
        ];


        gbbva_get_template('installment_theme/' . $theme . '.php', $settings);
    }
}


/**
 * Add gateway class to WooCommerce
 */
function gbbva_add_gateway_class($methods)
{
    $methods[] = 'GBBVA_Payment_Gateway';
    return $methods;
}
add_filter('woocommerce_payment_gateways', 'gbbva_add_gateway_class');

/**
 * Add payment iframe script
 */
function gbbva_add_payment_iframe_script()
{
    if (!is_checkout()) return;


    wp_enqueue_script('gbbva-checkout-iframe', plugins_url('assets/js/checkout.js', __FILE__), array('jquery'), GBBVA_VERSION, true);
}

/**
 * Get card image HTML
 */

function gbbva_get_card_image($card_key, $args = array())
{
    if (!is_string($card_key)) {
        return '';
    }

    $card_file = sanitize_file_name($card_key) . '.png';
    $image_path = GBBVA_PLUGIN_DIR . 'assets/images/cards/' . $card_file;
    $image_url = GBBVA_PLUGIN_URL . 'assets/images/cards/' . $card_file;
    
    // If the specific card image doesn't exist, use default
    if (!file_exists($image_path)) {
        $image_url = GBBVA_PLUGIN_URL . 'assets/images/cards/default.png';
    }
    
    $default_args = array(
        'src' => esc_url($image_url),
        'alt' => esc_attr($card_key),
        'class' => 'gbbva-card-image'
    );
    $args = wp_parse_args($args, $default_args);

    $html_attributes = array_map(
        function ($key, $value) {
            return sprintf(
                '%s="%s"',
                esc_attr($key),
                esc_attr($value)
            );
        },
        array_keys($args),
        $args
    );

    return sprintf('<img %s>', implode(' ', $html_attributes));
}
/**
 * Handle AJAX request for internal API
 */
function gbbva_internal_api_request()
{
    require_once GBBVA_PLUGIN_DIR . 'vendor/include.php';

    if (!check_ajax_referer('gbbva_internal_api_request', 'iapi_xfvv', false)) {
        wp_send_json('INSUFFICIENT PERMISSION');
    }

    try {
        $api = new \Eticsoft\Sanalpospro\InternalApi();
        $response = ($api->run())->getResponse();
        wp_send_json($response);
    } catch (Exception $e) {
        wp_send_json(array(
            'status' => 'error',
            'message' => $e->getMessage()
        ));
    }
}

/**
 * Enqueue plugin styles
 */
function gbbva_enqueue_styles()
{
    if (is_checkout()) {
        wp_enqueue_style('gbbva-payment-styles', GBBVA_PLUGIN_URL . 'assets/css/garantibbva-payment.css', array(), GBBVA_VERSION);
    }
}

/**
 * Get template path
 */
function gbbva_get_template_path()
{
    return GBBVA_PLUGIN_DIR . 'templates/';
}

/**
 * Get template
 */
function gbbva_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
    if (!$template_path) {
        $template_path = 'woocommerce/garantibbva/';
    }

    if (!$default_path) {
        $default_path = gbbva_get_template_path();
    }


    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name
        )
    );


    if (!$template) {
        $template = $default_path . $template_name;
    }

    if ($args && is_array($args)) {
        extract($args);
    }

    include($template);
}





