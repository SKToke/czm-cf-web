document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('noticesContainer')){
        document.getElementById('noticesContainer').addEventListener('click', function (event) {
            if (event.target.matches('[data-notice-id]')) {
                event.preventDefault();
                var noticeId = event.target.getAttribute('data-notice-id');
                var detailsSection = document.getElementById('details-' + noticeId);

                if (detailsSection) {
                    detailsSection.style.display = (detailsSection.style.display === 'none') ? 'block' : 'none';
                }
            }
        });
    }
});
