<?php
/**
 * GarantiBBVA BBVA Payment Gateway Uninstall
 *
 * Uninstalling the GarantiBBVA BBVA plugin deletes options, tables, and user metadata.
 *
 * @package GarantiBBVA
 */
if (!defined('ABSPATH')) exit;
// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}


// Delete plugin options
delete_option('GBBVA_PUBLIC_KEY');
delete_option('GBBVA_SECRET_KEY');
delete_option('GBBVA_TOKEN');
delete_option('GBBVA_ORDER_STATUS');
delete_option('GBBVA_CURRENCY_CONVERT');
delete_option('GBBVA_SHOWINSTALLMENTSTABS');
delete_option('GBBVA_PAYMENTPAGETHEME');
delete_option('GBBVA_INSTALLMENTS');


// Delete WooCommerce specific options
delete_option('woocommerce_garantibbva_settings');
delete_option('woocommerce_gbbva_settings');

// Delete transients
delete_transient('garantibbva_api_token');

// Clear scheduled hooks
wp_clear_scheduled_hook('garantibbva_daily_cleanup');