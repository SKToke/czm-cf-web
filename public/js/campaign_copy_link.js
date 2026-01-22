document.addEventListener('DOMContentLoaded', function() {
    var copyButtons = document.querySelectorAll('.copyButton');

    copyButtons.forEach(function(button) {
        button.addEventListener('click', function () {
            var urlToCopy = this.getAttribute('data-url');

            navigator.clipboard.writeText(urlToCopy)
                .then(function() {
                    alert('Link copied to clipboard: ' + urlToCopy);
                })
                .catch(function(error) {
                    alert('Failed to copy link to clipboard. Please try again.');
                });
        });
    });
});
