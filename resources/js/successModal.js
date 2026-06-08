import { Modal } from 'bootstrap';

document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById('payment-successful');
    if (el) {
        const modal = new Modal(el);
        modal.show();
    }
});
