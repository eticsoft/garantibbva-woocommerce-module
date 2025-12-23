<?php
if (!defined('ABSPATH')) {
    exit;
}

wp_enqueue_style(
    'garantibbva-installment-classic',
    GBBVA_PLUGIN_URL . 'assets/css/installment-classic.css',
    array(),
    GBBVA_VERSION
);

global $product;
if (!$product) return;

$gbbva_classic_price = $product->get_price();
$gbbva_classic_installments = json_decode(empty($gbbva_classic_installments) ? '[]' : $gbbva_classic_installments, true);
$gbbva_classic_all_card_families = [
    'world', 'axess', 'bonus', 'cardfinans', 'maximum',
    'paraf', 'saglamcard', 'advantage', 'combo', 'miles-smiles'
];

// Tüm kart aileleri için taksit seçeneklerini kontrol et
$gbbva_classic_any_installment_available = false;
foreach($gbbva_classic_all_card_families as $gbbva_classic_card_key) {
    if(!empty($gbbva_classic_installments[$gbbva_classic_card_key])) {
        $gbbva_classic_card_installments = array_filter($gbbva_classic_installments[$gbbva_classic_card_key], function($gbbva_classic_installment) {
            return $gbbva_classic_installment['gateway'] !== 'off';
        });
        if(!empty($gbbva_classic_card_installments)) {
            $gbbva_classic_any_installment_available = true;
            break;
        }
    }
}
?>

<div data-garantibbva-wrapper>
    <?php if(!$gbbva_classic_any_installment_available) : ?>
        <div data-garantibbva-no-installment style="padding: 20px; text-align: center;">
            <p style="margin: 0; font-size: 14px; color: #666;">
                <?php esc_html_e('No installment options are available for this product.', 'garanti-payment-gateway-for-woocommerce'); ?>
            </p>
        </div>
    <?php else : ?>
    <div data-garantibbva-container>
        <?php foreach($gbbva_classic_all_card_families as $gbbva_classic_card_key) :
            $gbbva_classic_has_any_installment = false;


            if(!empty($gbbva_classic_installments[$gbbva_classic_card_key])) {
                $gbbva_classic_card_installments = array_filter($gbbva_classic_installments[$gbbva_classic_card_key], function($gbbva_classic_installment) {
                    return $gbbva_classic_installment['gateway'] !== 'off';
                });

                if(!empty($gbbva_classic_card_installments)) {
                    $gbbva_classic_has_any_installment = true;
                }
            }

            if(!$gbbva_classic_has_any_installment) continue;

            echo '<div data-garantibbva-card>';
            echo '<table data-garantibbva-table>';
            echo '<thead>';
            echo '<tr>';
            echo '<td colspan="3">';
            echo wp_kses_post(gbbva_get_card_image($gbbva_classic_card_key));
            echo '</td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td width="33.33%">' . esc_html__('Installment', 'garanti-payment-gateway-for-woocommerce') . '</td>';
            echo '<td width="33.33%">' . esc_html__('Monthly Payment', 'garanti-payment-gateway-for-woocommerce') . '</td>';
            echo '<td width="33.33%">' . esc_html__('Total', 'garanti-payment-gateway-for-woocommerce') . '</td>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            for($gbbva_classic_i = 1; $gbbva_classic_i <= 12; $gbbva_classic_i++) {
                $gbbva_classic_installment_exists = false;
                $gbbva_classic_monthly_payment = '-';
                $gbbva_classic_total_amount = '-';

                foreach($gbbva_classic_card_installments as $gbbva_classic_installment) {
                    if($gbbva_classic_installment['months'] == $gbbva_classic_i) {
                        $gbbva_classic_installment_exists = true;
                        if($gbbva_classic_i == 1 && $gbbva_classic_installment['gateway_fee_percent'] == 0) {
                            $gbbva_classic_total = $gbbva_classic_price;
                            $gbbva_classic_monthly = $gbbva_classic_total;
                        } else {
                            // Yeni formül: (Anapara × 100) / (100 - Komisyon Oranı)
                            $gbbva_classic_total = ($gbbva_classic_price * 100) / (100 - $gbbva_classic_installment['gateway_fee_percent']);
                            $gbbva_classic_monthly = $gbbva_classic_total/$gbbva_classic_i;
                        }
                        $gbbva_classic_monthly_payment = wp_kses_post(wc_price($gbbva_classic_monthly));
                        $gbbva_classic_total_amount = wp_kses_post(wc_price($gbbva_classic_total));
                        break;
                    }
                }

                echo '<tr>';
                echo '<td>' . ($gbbva_classic_i == 1 ? 
                     esc_html__('Cash', 'garanti-payment-gateway-for-woocommerce') : 
                     sprintf(
                         /* translators: %d: Installment number */
                         esc_html__('%d. Installment', 'garanti-payment-gateway-for-woocommerce'),
                         esc_html($gbbva_classic_i)
                     )) . '</td>';
                echo '<td>' . wp_kses_post($gbbva_classic_monthly_payment) . '</td>';
                echo '<td>' . wp_kses_post($gbbva_classic_total_amount) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        endforeach; ?>
    </div>

    <div data-garantibbva-note>
        <p><?php esc_html_e('* Installment amounts are estimated and may vary according to your bank\'s campaigns and interest rates.', 'garanti-payment-gateway-for-woocommerce'); ?></p>
    </div>
    <?php endif; ?>
</div>