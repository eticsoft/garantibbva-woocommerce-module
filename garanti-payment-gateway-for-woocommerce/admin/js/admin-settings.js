

document.addEventListener('DOMContentLoaded', function () {
    const wpBodyContent = document.querySelector('#wpbody-content');
    if (wpBodyContent) {
        wpBodyContent.style.padding = '0';
    }

    const wpFooterDisplayHidden = document.querySelector("#wpfooter");
    if (wpFooterDisplayHidden) {
        wpFooterDisplayHidden.style.display = "none";
    }
});
