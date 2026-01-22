document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const paymentRoute = document.querySelector('#filter-payments-form').getAttribute('data-payment-route');

    startDateInput.addEventListener('change', function() {
        submitForm();
    });

    endDateInput.addEventListener('change', function() {
        submitForm();
    });

    document.querySelector('#reset-filter-btn').addEventListener('click', function() {
        document.querySelector('#filter-payments-form').reset();
        window.location.href = paymentRoute;
    });

    window.onload = function() {
        document.querySelector('#filter-payments-form').reset();
    };

    function submitForm() {
        const form = document.querySelector('#filter-payments-form');
        const actionUrl = form.getAttribute('action');
        const formData = new FormData(form);

        var startInput = startDateInput.value.toString();
        var endInput = endDateInput.value.toString();

        document.getElementById('payment-start-date').value = startInput;
        document.getElementById('payment-end-date').value = endInput;

        const searchParams = new URLSearchParams();
        for (const pair of formData) {
            searchParams.append(pair[0], pair[1].toString());
        }

        fetch(actionUrl + '?' + searchParams,{
            method: 'GET',
        })
            .then(response => response.text())
            .then(html => {
                document.querySelector('#paymentsContainer').innerHTML = html;
            })
            .catch(error => {
                console.error(error);
            });
    }
});
