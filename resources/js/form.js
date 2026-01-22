document.addEventListener('DOMContentLoaded', function() {
    try{
        if (flashMessageData) {
            displayFlashMessage(
                JSON.parse(flashMessageData).message,
                JSON.parse(flashMessageData).status
            );
        }
    } catch (e) {}
    document.querySelectorAll('.ajax-form').forEach(form => {
        attachFormSubmitHandler(form);
    });
});

function attachFormSubmitHandler(form) {
    const subscribeUrl = form.getAttribute('data-action-url');
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(form);
        const contactTypeValue = form.elements['contact_type'] ? form.elements['contact_type'].value : null;

        fetch(subscribeUrl, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData,
        })
            .then(response => {
                if (!response.ok) {
                    throw response;
                }
                if (contactTypeValue && typeof grecaptcha !== 'undefined') {
                    grecaptcha.reset();
                }
                return response.json();
            })
            .then(data => {
                if (data.errors) {
                    displayValidationErrors(data.errors);
                } else {
                    displayFlashMessage(data.message, data.status);
                }
                // Reset the form after processing the response
                form.reset();
                if (contactTypeValue !== null && form.elements['contact_type']) {
                    form.elements['contact_type'].value = contactTypeValue;
                }
                window.scrollTo(0, 0);
            })
            .catch(error => {
                error.json().then(err => {
                    if (err.errors) {
                        displayValidationErrors(err.errors);
                    } else {
                        console.error('Error:', err);
                    }
                });
            });
    });
}


function displayFlashMessage(message, status) {
    const flashMessagesDiv = document.getElementById('flash-messages');
    flashMessagesDiv.innerHTML = `<div class="${status === 'success' ? 'alert alert-success' : 'alert alert-danger'} mb-0">${message}</div>`;
    window.scrollTo(0, 0);
    setTimeout(() => {
        flashMessagesDiv.innerHTML = '';
    }, 5000);
}

function displayValidationErrors(errors) {
    const messages = Object.keys(errors).reduce((acc, key) => {
        return acc + errors[key].map(message => `<li>${message}</li>`).join('');
    }, '');

    const flashMessagesDiv = document.getElementById('flash-messages');
    flashMessagesDiv.innerHTML = `<div class="alert alert-danger"><ul>${messages}</ul></div>`;
    window.scrollTo(0, 0);
    setTimeout(() => {
        flashMessagesDiv.innerHTML = '';
    }, 5000);
}
