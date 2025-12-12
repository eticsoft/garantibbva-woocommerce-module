<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * GarantiBBVA Modern Installment Theme Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/garantibbva/installment_theme/modern.php.
 *
 * @package GarantiBBVA
 * @version 2.0.1
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

$price = $product->get_price();
$installments = json_decode(empty($installments) ? '[]' : $installments, true);
//print_r($installments);

$all_card_families = [
    'world', 'axess', 'bonus', 'cardfinans', 'maximum',
    'paraf', 'saglamcard', 'advantage', 'combo', 'miles-smiles'
];

// Tüm kart aileleri için taksit seçeneklerini kontrol et
$any_installment_available = false;
foreach($all_card_families as $card_key) {
    if(!empty($installments[$card_key])) {
        $card_installments = array_filter($installments[$card_key], function($installment) {
            return $installment['gateway'] !== 'off';
        });
        if(!empty($card_installments)) {
            $any_installment_available = true;
            break;
        }
    }
}
?>

<div class="gbbva-installment-container">
    <?php if(!$any_installment_available) : ?>
        <div class="gbbva-no-installment" style="padding: 20px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #666;">
                <?php esc_html_e('No installment options are available for this product.', 'garanti-payment-module'); ?>
            </p>
        </div>
    <?php else : ?>
    <div class="gbbva-installment-tabs">
        <div class="gbbva-tab-header">
            <?php foreach($all_card_families as $card_key) :
                $has_any_installment = false;


                if(!empty($installments[$card_key])) {
                    $card_installments = array_filter($installments[$card_key], function($installment) {
                        return $installment['gateway'] !== 'off';
                    });

                    if(!empty($card_installments)) {
                        $has_any_installment = true;
                    }
                }


                if (!$has_any_installment) continue;
            ?>
                <div class="gbbva-tab-item" data-tab="<?php echo esc_attr($card_key); ?>">
                    <?php echo wp_kses_post(gbbva_get_card_image($card_key, ['style' => 'height: 30px;'])); ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="gbbva-tab-content">
            <?php foreach($all_card_families as $card_key) : 
                $has_any_installment = false;
                
               
                if(!empty($installments[$card_key])) {
                    $card_installments = array_filter($installments[$card_key], function($installment) {
                        return $installment['gateway'] !== 'off';
                    });

                    if(!empty($card_installments)) {
                        $has_any_installment = true;
                    }
                }
                
                
                if (!$has_any_installment) continue;
            ?>
                <div class="gbbva-tab-pane" data-tab-content="<?php echo esc_attr($card_key); ?>">
                    <table class="gbbva-installment-table">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Installment', 'garanti-payment-module'); ?></th>
                                <th><?php esc_html_e('Monthly Payment', 'garanti-payment-module'); ?></th>
                                <th><?php esc_html_e('Total', 'garanti-payment-module'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            for($i = 1; $i <= 12; $i++) {
                                $installment_exists = false;
                                $monthly_payment = '-';
                                $total_amount = '-';

                                foreach($card_installments as $installment) {
                                    if($installment['months'] == $i) {
                                        $installment_exists = true;
                                        if($i == 1 && $installment['gateway_fee_percent'] == 0) {
                                            $total = $price;
                                            $monthly = $total;
                                        } else {
                                            // Yeni formül: (Anapara × 100) / (100 - Komisyon Oranı)
                                            $total = ($price * 100) / (100 - $installment['gateway_fee_percent']);
                                            $monthly = $total/$i;
                                        }
                                        $monthly_payment = wp_kses_post(wc_price($monthly));
                                        $total_amount = wp_kses_post(wc_price($total));
                                        break;
                                    }
                                }

                                echo '<tr>';
                                echo '<td>' . ($i == 1 ? 
                                     esc_html__('Cash', 'garanti-payment-module') : 
                                     sprintf(
                                         /* translators: %d: Installment number */
                                         esc_html__('%d. Installment', 'garanti-payment-module'),
                                         esc_html($i)
                                     )) . '</td>';
                                echo '<td>' . wp_kses_post($monthly_payment) . '</td>';
                                echo '<td>' . wp_kses_post($total_amount) . '</td>';
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
        <p><?php esc_html_e('* Installment amounts are estimated and may vary according to your bank\'s campaigns and interest rates.', 'garanti-payment-module'); ?></p>
    </div>
    <?php endif; ?>
</div>

