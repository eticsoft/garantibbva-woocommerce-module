<?php
/**
 * GarantiBBVA Modern Installment Theme Template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/garantibbva/installment_theme/modern.php.
 *
 * @package GarantiBBVA
 * @version 0.1.2
 */

if (!defined('ABSPATH')) {
    exit;
}

wp_enqueue_style(
    'garantibbva-installment-modern',
    plugins_url('assets/css/installment-modern.css', dirname(__FILE__, 2)),
    array(),
    GBBVA_VERSION
);

global $product;
if (!$product) return;

$price = $product->get_price();
$installments = json_decode(empty($installments) ? '[]' : $installments, true);

$all_card_families = [
    'world', 'axess', 'bonus', 'cardfinans', 'maximum',
    'paraf', 'saglamcard', 'advantage', 'combo', 'miles-smiles'
];

?>

<div class="gbbva-installment-container">
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
                    <?php echo wp_kses_post(garantibbva_get_card_image($card_key, ['style' => 'height: 30px;'])); ?>
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
                                <th><?php esc_html_e('Installment', 'garantibbva'); ?></th>
                                <th><?php esc_html_e('Monthly Payment', 'garantibbva'); ?></th>
                                <th><?php esc_html_e('Total', 'garantibbva'); ?></th>
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
                                            $total = $price + (($price * $installment['gateway_fee_percent'])/100);
                                            $monthly = $total;
                                        } else {
                                            $total = $price * (1 + $installment['gateway_fee_percent']/100);
                                            $monthly = $total/$i;
                                        }
                                        $monthly_payment = wp_kses_post(wc_price($monthly));
                                        $total_amount = wp_kses_post(wc_price($total));
                                        break;
                                    }
                                }

                                echo '<tr>';
                                echo '<td>' . ($i == 1 ? 
                                     esc_html__('Cash', 'garantibbva') : 
                                     sprintf(
                                         /* translators: %d: Installment number */
                                         esc_html__('%d. Installment', 'garantibbva'),
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
        <p><?php esc_html_e('* Installment amounts are estimated and may vary according to your bank\'s campaigns and interest rates.', 'garantibbva'); ?></p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabItems = document.querySelectorAll('.gbbva-tab-item');
    const tabPanes = document.querySelectorAll('.gbbva-tab-pane');
    
   
    if (tabItems.length > 0) {
        tabItems[0].classList.add('active');
        const firstTabId = tabItems[0].getAttribute('data-tab');
        document.querySelector(`.gbbva-tab-pane[data-tab-content="${firstTabId}"]`).classList.add('active');
    }
    

    tabItems.forEach(item => {
        item.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
           
            tabItems.forEach(tab => tab.classList.remove('active'));
            this.classList.add('active');
            
           
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.querySelector(`.gbbva-tab-pane[data-tab-content="${tabId}"]`).classList.add('active');
        });
    });
});
</script>
