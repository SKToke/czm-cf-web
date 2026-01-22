function switchTab(activeTab, inactiveTab, activeContent, inactiveContent) {
    activeTab.classList.add('active');
    inactiveTab.classList.remove('active');
    activeContent.classList.remove('d-none');
    inactiveContent.classList.add('d-none');

    // Reset the reCAPTCHA
    if (typeof grecaptcha !== 'undefined') {
        grecaptcha.reset();
    }
}

const contactUsTab = document.getElementById('contactUsTab');
const zakatConsultancyTab = document.getElementById('zakatConsultancyTab');
const contactUsContent = document.getElementById('contactUsContent');
const zakatConsultancyContent = document.getElementById('zakatConsultancyContent');


const personalZakatCheckbox = document.getElementById('personalZakat');
const businessZakatCheckbox = document.getElementById('businessZakat');
const generalCheckbox = document.getElementById('general');

contactUsTab.addEventListener('click', () => {
    switchTab(contactUsTab, zakatConsultancyTab, contactUsContent, zakatConsultancyContent);
    document.getElementById('personalZakat').checked = false;
    document.getElementById('businessZakat').checked = false;
    document.getElementById('general').checked = true;

});

zakatConsultancyTab.addEventListener('click', () => {
    switchTab(zakatConsultancyTab, contactUsTab, zakatConsultancyContent, contactUsContent);
    document.getElementById('personalZakat').checked = true;
    document.getElementById('businessZakat').checked = false;
    document.getElementById('general').checked = false;
});

personalZakatCheckbox.addEventListener('click', () => {
    if (typeof grecaptcha !== 'undefined') {
        grecaptcha.reset();
    }
});

businessZakatCheckbox.addEventListener('click', () => {
    if (typeof grecaptcha !== 'undefined') {
        grecaptcha.reset();
    }
});

switchTab(contactUsTab, zakatConsultancyTab, contactUsContent, zakatConsultancyContent);
