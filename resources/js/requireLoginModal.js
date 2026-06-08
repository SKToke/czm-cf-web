import { Modal } from 'bootstrap';

document.addEventListener("DOMContentLoaded", function () {
    const requireLogin = document.querySelector('#require-login');
    const requireLoginButton = document.querySelector('#require-login-button');
    const requireRegister = document.querySelector('#require-register');

    const loginModalEl = document.getElementById('loginModal');
    const registerModalEl = document.getElementById('registerModal');

    const loginModal = loginModalEl ? new Modal(loginModalEl) : null;
    const registerModal = registerModalEl ? new Modal(registerModalEl) : null;

    function closeOtherModals() {
        const downloadModalEl = document.getElementById('downloadModal');
        if (downloadModalEl) {
            const downloadModal = Modal.getInstance(downloadModalEl) || new Modal(downloadModalEl);
            downloadModal.hide();
        }
        const subscribeCampaignModalEl = document.getElementById('subscribeCampaignModal');
        if (subscribeCampaignModalEl) {
            const subscribeCampaignModal = Modal.getInstance(subscribeCampaignModalEl) || new Modal(subscribeCampaignModalEl);
            subscribeCampaignModal.hide();
        }
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    }

    if (requireLogin && loginModal) {
        requireLogin.addEventListener('click', function (e) {
            e.preventDefault();
            closeOtherModals();
            loginModal.show();
        });
    }
    if (requireLoginButton && loginModal) {
        requireLoginButton.addEventListener('click', function (e) {
            e.preventDefault();
            closeOtherModals();
            loginModal.show();
        });
    }
    if (requireRegister && registerModal) {
        requireRegister.addEventListener('click', function (e) {
            e.preventDefault();
            closeOtherModals();
            registerModal.show();
        });
    }
});
