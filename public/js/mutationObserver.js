document.addEventListener("DOMContentLoaded", function() {
    document.body.addEventListener('click', function(event) {
        const addButton = event.target.closest('.has-many-footer .btn-success');
        if (addButton) {
            addButton.style.visibility = 'hidden';
        }
    });
});
