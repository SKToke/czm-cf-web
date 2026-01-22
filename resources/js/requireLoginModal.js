import { Modal } from 'bootstrap/js/src/modal.js';

document.addEventListener("DOMContentLoaded", function() {
    const requireLogin = document.querySelector('#require-login');
    const requireLoginButton = document.querySelector('#require-login-button');
    const requireRegister = document.querySelector('#require-register');

    if ( requireLogin ) {
        requireLogin.addEventListener('click', function(e) {
            if ($('#downloadModal')) {
                $('#downloadModal').modal('hide');
            }
            if ($('#subscribeCampaignModal')) {
                $('#subscribeCampaignModal').modal('hide');
            }
            $('.modal-backdrop').remove();
            $('#loginModal').modal('show');
        });
    }
    if ( requireLoginButton ) {
        requireLoginButton.addEventListener('click', function(e) {
            if ($('#downloadModal')) {
                $('#downloadModal').modal('hide');
            }
            if ($('#subscribeCampaignModal')) {
                $('#subscribeCampaignModal').modal('hide');
            }
            $('.modal-backdrop').remove();
            $('#loginModal').modal('show');
        });
    }
    if ( requireRegister ) {
        requireRegister.addEventListener('click', function(e) {
            $('.modal-backdrop').remove();
            $('#registerModal').modal('show');
        });
    }
});
