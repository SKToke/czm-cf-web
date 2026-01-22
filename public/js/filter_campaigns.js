document.addEventListener('DOMContentLoaded', function() {
    const inputsAndSelects = document.querySelectorAll('#filter-campaigns-form input, #filter-campaigns-form select');
    const campaignRoute = document.querySelector('#filter-campaigns-form').getAttribute('data-campaign-route');

    inputsAndSelects.forEach(element => {
        element.addEventListener('change', function() {
            submitForm();
        });
    });

    const searchInput = document.querySelector('#campaign_title');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            submitForm();
        });
    }

    document.querySelector('#reset-filter-btn').addEventListener('click', function() {
        document.querySelector('#filter-campaigns-form').reset();
        window.location.href = campaignRoute;
    });

    window.onload = function() {
        document.querySelector('#filter-campaigns-form').reset();
    };

    function submitForm() {
        const form = document.querySelector('#filter-campaigns-form');
        const actionUrl = form.getAttribute('action');
        const formData = new FormData(form);

        const searchParams = new URLSearchParams();
        for (const pair of formData) {
            searchParams.append(pair[0], pair[1].toString());
        }

        fetch(actionUrl + '?' + searchParams,{
            method: 'GET',
        })
            .then(response => response.text())
            .then(html => {
                document.querySelector('#case_list').innerHTML = html;
            })
            .catch(error => {
                console.error(error);
            });
    }
});
