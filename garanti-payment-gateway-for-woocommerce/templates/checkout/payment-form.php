<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * GarantiBBVA Payment Form Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/garantibbva/checkout/payment-form.php.
 *
 * @package GarantiBBVA
 * @version 2.0.1
 */
?>

<div id="gbbva-payment-form" class="gbbva-payment-form">
    <?php if (!empty($description)) : ?>
        <div class="gbbva-payment-description">
            <?php echo wp_kses_post(wpautop(wp_kses_post($description))); ?>
        </div>
    <?php endif; ?>
</div> 