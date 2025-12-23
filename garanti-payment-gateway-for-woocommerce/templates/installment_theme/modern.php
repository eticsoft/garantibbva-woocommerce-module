<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * GarantiBBVA Modern Installment Theme Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/garantibbva/installment_theme/modern.php.
 *
 * @package GarantiBBVA
 * @version 2.1.0
 */

// Enqueue CSS file
wp_enqueue_style(
    'garantibbva-installment-modern',
    GBBVA_PLUGIN_URL . 'assets/css/installment-modern.css',
    array(),
    GBBVA_VERSION
);

// Enqueue JS file
wp_enqueue_script(
    'garantibbva-installment-modern',
    GBBVA_PLUGIN_URL . 'assets/js/installment-modern.js',
    array('jquery'),
    GBBVA_VERSION,
    true
);

global $product;
if (!$product) return;

$gbbva_modern_price = $product->get_price();
$gbbva_modern_installments = json_decode(empty($gbbva_modern_installments) ? '[]' : $gbbva_modern_installments, true);
//print_r($gbbva_modern_installments);

$gbbva_modern_all_card_families = [
    'world', 'axess', 'bonus', 'cardfinans', 'maximum',
    'paraf', 'saglamcard', 'advantage', 'combo', 'miles-smiles'
];

// Tüm kart aileleri için taksit seçeneklerini kontrol et
$gbbva_modern_any_installment_available = false;
foreach($gbbva_modern_all_card_families as $gbbva_modern_card_key) {
    if(!empty($gbbva_modern_installments[$gbbva_modern_card_key])) {
        $gbbva_modern_card_installments = array_filter($gbbva_modern_installments[$gbbva_modern_card_key], function($gbbva_modern_installment) {
            return $gbbva_modern_installment['gateway'] !== 'off';
        });
        if(!empty($gbbva_modern_card_installments)) {
            $gbbva_modern_any_installment_available = true;
            break;
        }
    }
}
?>

<div class="gbbva-installment-container">
    <?php if(!$gbbva_modern_any_installment_available) : ?>
        <div class="gbbva-no-installment" style="padding: 20px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #666;">
                <?php esc_html_e('No installment options are available for this product.', 'garanti-payment-gateway-for-woocommerce'); ?>
            </p>
        </div>
    <?php else : ?>
    <div class="gbbva-installment-tabs">
        <div class="gbbva-tab-header">
            <?php foreach($gbbva_modern_all_card_families as $gbbva_modern_card_key) :
                $gbbva_modern_has_any_installment = false;


                if(!empty($gbbva_modern_installments[$gbbva_modern_card_key])) {
                    $gbbva_modern_card_installments = array_filter($gbbva_modern_installments[$gbbva_modern_card_key], function($gbbva_modern_installment) {
                        return $gbbva_modern_installment['gateway'] !== 'off';
                    });

                    if(!empty($gbbva_modern_card_installments)) {
                        $gbbva_modern_has_any_installment = true;
                    }
                }


                if (!$gbbva_modern_has_any_installment) continue;
            ?>
                <div class="gbbva-tab-item" data-tab="<?php echo esc_attr($gbbva_modern_card_key); ?>">
                    <?php echo wp_kses_post(gbbva_get_card_image($gbbva_modern_card_key, ['style' => 'height: 30px;'])); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="gbbva-tab-content">
            <?php foreach($gbbva_modern_all_card_families as $gbbva_modern_card_key) : 
                $gbbva_modern_has_any_installment = false;
                
               
                if(!empty($gbbva_modern_installments[$gbbva_modern_card_key])) {
                    $gbbva_modern_card_installments = array_filter($gbbva_modern_installments[$gbbva_modern_card_key], function($gbbva_modern_installment) {
                        return $gbbva_modern_installment['gateway'] !== 'off';
                    });

                    if(!empty($gbbva_modern_card_installments)) {
                        $gbbva_modern_has_any_installment = true;
                    }
                }
                
                
                if (!$gbbva_modern_has_any_installment) continue;
            ?>
                <div class="gbbva-tab-pane" data-tab-content="<?php echo esc_attr($gbbva_modern_card_key); ?>">
                    <table class="gbbva-installment-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Installment', 'garanti-payment-gateway-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Monthly Payment', 'garanti-payment-gateway-for-woocommerce'); ?></th>
                                <th><?php esc_html_e('Total', 'garanti-payment-gateway-for-woocommerce'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            for($gbbva_modern_i = 1; $gbbva_modern_i <= 12; $gbbva_modern_i++) {
                                $gbbva_modern_installment_exists = false;
                                $gbbva_modern_monthly_payment = '-';
                                $gbbva_modern_total_amount = '-';

                                foreach($gbbva_modern_card_installments as $gbbva_modern_installment) {
                                    if($gbbva_modern_installment['months'] == $gbbva_modern_i) {
                                        $gbbva_modern_installment_exists = true;
                                        if($gbbva_modern_i == 1 && $gbbva_modern_installment['gateway_fee_percent'] == 0) {
                                            $gbbva_modern_total = $gbbva_modern_price;
                                            $gbbva_modern_monthly = $gbbva_modern_total;
                                        } else {
                                            // Yeni formül: (Anapara × 100) / (100 - Komisyon Oranı)
                                            $gbbva_modern_total = ($gbbva_modern_price * 100) / (100 - $gbbva_modern_installment['gateway_fee_percent']);
                                            $gbbva_modern_monthly = $gbbva_modern_total/$gbbva_modern_i;
                                        }
                                        $gbbva_modern_monthly_payment = wp_kses_post(wc_price($gbbva_modern_monthly));
                                        $gbbva_modern_total_amount = wp_kses_post(wc_price($gbbva_modern_total));
                                        break;
                                    }
                                }

                                echo '<tr>';
                                echo '<td>' . ($gbbva_modern_i == 1 ? 
                                     esc_html__('Cash', 'garanti-payment-gateway-for-woocommerce') : 
                                     sprintf(
                                         /* translators: %d: Installment number */
                                         esc_html__('%d. Installment', 'garanti-payment-gateway-for-woocommerce'),
                                         esc_html($gbbva_modern_i)
                                     )) . '</td>';
                                echo '<td>' . wp_kses_post($gbbva_modern_monthly_payment) . '</td>';
                                echo '<td>' . wp_kses_post($gbbva_modern_total_amount) . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="gbbva-installment-note">
        <p><?php esc_html_e('* Installment amounts are estimated and may vary according to your bank\'s campaigns and interest rates.', 'garanti-payment-gateway-for-woocommerce'); ?></p>
    </div>
    <?php endif; ?>
</div>

