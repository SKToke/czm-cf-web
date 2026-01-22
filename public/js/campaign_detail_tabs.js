document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(navLink) {
        navLink.addEventListener('click', function(e) {
            e.preventDefault();
            var tabId = navLink.id;
            var campaignSlug = document.querySelector('.case-detail-tabs').getAttribute('data-campaign-slug');

            fetch(navLink.getAttribute('href') + '?slug=' + campaignSlug)
                .then(function(response) {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(function(response) {
                    document.querySelector('#case_tab_content').innerHTML = response;
                    navLinks.forEach(function(link) {
                        link.classList.remove('active');
                    });
                    navLink.classList.add('active');
                })
                .catch(function(error) {
                    console.error('There was a problem with the fetch operation:', error.message);
                });
        });
    });
});
