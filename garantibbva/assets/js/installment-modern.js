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