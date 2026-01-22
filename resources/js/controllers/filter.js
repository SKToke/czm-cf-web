document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const targetContainerId = form.getAttribute('data-target-container');
    const targetContainer = document.getElementById(targetContainerId);
    const resetButton = document.getElementById('resetButton');
    if (form) {
        form.addEventListener('change', function() {
            fetchNotices();
        });

        resetButton.addEventListener('click', function() {
            form.reset();
            window.location.href = form.action;
        });
    }

    document.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' && e.target.closest('.pagination')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            fetchNotices(url.search);
        }
    });


    function fetchNotices(queryString = '') {
        const formData = new FormData(form);
        const params = new URLSearchParams(queryString);

        for (const [key, value] of formData) {
            if (!params.has(key)) {
                params.append(key, value);
            }
        }

        const requestUrl = `${form.action}?${params}`;

        fetch(requestUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (targetContainer) {
                    targetContainer.innerHTML = data;
                }
            })
            .catch(error => console.error('Fetch error', error));
    }
});
