<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * GarantiBBVA Payment iFrame Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/garantibbva/checkout/payment-iframe.php.
 *
 * @package GarantiBBVA
 * @version 2.0.1
 */
?>

<div id="payment-iframe-container" class="gbbva-iframe-container">
    <div class="gbbva-iframe-wrapper">
        <div class="gbbva-iframe-header">
            <div class="gbbva-header-spacer"></div>
            <button type="button" class="gbbva-close-iframe" onclick="document.getElementById('payment-iframe-container').remove()">
                <?php esc_html_e('×', 'garanti-payment-module'); ?>
            </button>
        </div>
        
        <div class="gbbva-iframe-content">
            <div class="gbbva-loading">
                <div class="gbbva-spinner"></div>
                <p><?php esc_html_e('Loading payment page...', 'garanti-payment-module'); ?></p>
            </div>
            <iframe 
                src="<?php echo esc_url($payment_link); ?>" 
                sandbox="allow-scripts allow-top-navigation allow-same-origin allow-forms"
                class="gbbva-payment-iframe" 
                frameborder="0" 
                allow="payment" 
                onload="document.querySelector('.gbbva-loading').style.display = 'none';"
            ></iframe>
        </div>
    </div>
</div> 