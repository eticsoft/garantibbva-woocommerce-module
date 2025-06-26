document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.garantibbva-card-installment-wrapper').forEach(function(wrapper, index) {
        if(index == 0) {
            wrapper.classList.add('garantibbva-installment-card-wrapper-active');
        } else {
            wrapper.classList.add('garantibbva-installment-card-wrapper-inactive');
        }
    });

    document.querySelectorAll('.garantibbva-card-family-wrapper').forEach(function(wrapper, index) {
        if(index == 0) {
            wrapper.classList.add('garantibbva-card-family-wrapper-active');
        } else {
            wrapper.classList.add('garantibbva-card-family-wrapper-inactive');
        }
    });
    
    // New tab functionality
    const tabItems = document.querySelectorAll('.gbbva-tab-item');
    const tabPanes = document.querySelectorAll('.gbbva-tab-pane');
    
    // Activate first tab by default
    if (tabItems.length > 0) {
        tabItems[0].classList.add('active');
        const firstTabId = tabItems[0].getAttribute('data-tab');
        document.querySelector(`.gbbva-tab-pane[data-tab-content="${firstTabId}"]`).classList.add('active');
    }
    
    // Tab click event handlers
    tabItems.forEach(item => {
        item.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Update active tab
            tabItems.forEach(tab => tab.classList.remove('active'));
            this.classList.add('active');
            
            // Update active content
            tabPanes.forEach(pane => pane.classList.remove('active'));
            document.querySelector(`.gbbva-tab-pane[data-tab-content="${tabId}"]`).classList.add('active');
        });
    });
});

function selectCardFamily(selector, cardFamilyWrapper) {
    document.querySelectorAll('.garantibbva-card-installment-wrapper').forEach(function(wrapper, index) {
        wrapper.classList.add('garantibbva-installment-card-wrapper-inactive');
    });

    document.querySelector(selector).classList.remove('garantibbva-installment-card-wrapper-inactive');
    document.querySelector(selector).classList.add('garantibbva-installment-card-wrapper-active');

    document.querySelectorAll('.garantibbva-card-family-wrapper').forEach(function(wrapper, index) {
        wrapper.classList.remove('garantibbva-card-family-wrapper-active');
        wrapper.classList.add('garantibbva-card-family-wrapper-inactive');
    });

    document.querySelector(cardFamilyWrapper).classList.add('garantibbva-card-family-wrapper-active');
} 