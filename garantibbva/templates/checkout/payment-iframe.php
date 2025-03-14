<?php
/**
 * GarantiBBVA Payment iFrame Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/garantibbva/checkout/payment-iframe.php.
 *
 * @package GarantiBBVA
 * @version 0.1.2
 */

defined('ABSPATH') || exit;
?>

<div id="payment-iframe-container" class="gbbva-iframe-container">
    <div class="gbbva-iframe-wrapper">
        <div class="gbbva-iframe-header">
            <div class="gbbva-header-spacer"></div>
            <button type="button" class="gbbva-close-iframe" onclick="document.getElementById('payment-iframe-container').remove()">
                <?php esc_html_e('×', 'garantibbva'); ?>
            </button>
        </div>
        
        <div class="gbbva-iframe-content">
            <div class="gbbva-loading">
                <div class="gbbva-spinner"></div>
                <p><?php esc_html_e('Loading payment page...', 'garantibbva'); ?></p>
            </div>
            <iframe 
                src="<?php echo esc_url($payment_link); ?>" 
                class="gbbva-payment-iframe" 
                frameborder="0" 
                allow="payment" 
                onload="document.querySelector('.gbbva-loading').style.display = 'none';"
            ></iframe>
        </div>
    </div>
</div> 