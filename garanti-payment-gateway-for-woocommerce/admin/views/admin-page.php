<?php
/**
 * Admin page template
 *
 * @package GarantiBBVA
 */

defined('ABSPATH') || exit;
?>
<div class="wrap gbbva-admin-wrap">
    <div id="root"
         data-token="<?php echo esc_attr($token); ?>"
         data-platform="wooCommerce"
         data-website="<?php echo esc_url($site_url); ?>"
         data-app-id="103"
         data-program-id="3">
    </div>
</div>
