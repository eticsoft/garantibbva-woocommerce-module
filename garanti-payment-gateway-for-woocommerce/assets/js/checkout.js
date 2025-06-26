/**
 * GarantiBBVA Payment Gateway checkout script
 * Handles the payment iframe and communication with the payment provider
 */
(function($) {
    $(document).ready(function() {
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            return;
        }
        
        $('form.checkout').on('checkout_place_order_success', function(event, response) {
            console.log("response", response.redirect_url);
            

            if (response.iframe_html) {
                $('body').append(response.iframe_html);
                
                // Add loading indicator
                $('.gbbva-loading').show();
                
                // Handle iframe messages
                window.addEventListener("message", function (event) {
                    if ((event.origin === "https://pay.paythor.com")) {
                        if(event.data.type === 'opensource') {
                            if(event.data.tdsForm && event.data.form_selector_id === 'three_d_form') {
                                const form = $(event.data.tdsForm);
                                $('body').append(form);
                                form.submit();
                            }
                        }
                        
                        if (event.data.isSuccess) {
                            $('.gbbva-close-iframe').hide();
                            window.location.href = response.redirect_url + "&p_id=" + encodeURIComponent(event.data.processID);
                        } else if (event.data.error) {
                            $('.gbbva-iframe-content').append('<div class="gbbva-error-message">' + event.data.error + '</div>');
                        }
                    }
                }, false);
                
                return false;
            }
            return true;
        });
    });
})(jQuery);