jQuery(document).ready(function($) {
    // Show popup when button is clicked
    $('#gbbva-show-instructions').on('click', function(e) {
        e.preventDefault();
        $('#gbbva-popup').css('display', 'flex');
    });
    
    // Close popup when X is clicked
    $('.gbbva-popup-close').on('click', function(e) {
        e.preventDefault();
        $('#gbbva-popup').css('display', 'none');
    });
    
    // Close popup when clicking outside
    $('#gbbva-popup').on('click', function(e) {
        if (e.target === this) {
            $(this).css('display', 'none');
        }
    });
    
    // Close on ESC key
    $(document).keyup(function(e) {
        if (e.key === "Escape") {
            $('#gbbva-popup').css('display', 'none');
        }
    });
}); 