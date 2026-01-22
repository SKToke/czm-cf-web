document.addEventListener('DOMContentLoaded', function() {
    const logoutLink = document.querySelector('#logout-link');
    if (logoutLink) {
        const logoutForm = document.querySelector('#logout-form');
        logoutLink.addEventListener('click', function(event) {
            event.preventDefault();
            logoutForm.submit();
        });
    }
});
