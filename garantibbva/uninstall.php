<?php
/**
 * Garanti BBVA Payment Gateway Uninstall
 *
 * Uninstalling the Garanti BBVA plugin deletes options, tables, and user metadata.
 *
 * @package GarantiBBVA
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete plugin options
delete_option('GARANTIBBVA_PUBLIC_KEY');
delete_option('GARANTIBBVA_SECRET_KEY');
delete_option('GARANTIBBVA_TOKEN');
delete_option('GARANTIBBVA_ORDER_STATUS');
delete_option('GARANTIBBVA_CURRENCY_CONVERT');
delete_option('GARANTIBBVA_SHOWINSTALLMENTSTABS');
delete_option('GARANTIBBVA_PAYMENTPAGETHEME');
delete_option('GARANTIBBVA_INSTALLMENTS');

// Delete WooCommerce specific options
delete_option('woocommerce_garantibbva_settings');
delete_option('woocommerce_gbbva_settings');

// Delete transients
delete_transient('garantibbva_api_token');

// Clear scheduled hooks
wp_clear_scheduled_hook('garantibbva_daily_cleanup');