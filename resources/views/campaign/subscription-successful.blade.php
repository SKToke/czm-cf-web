<div class="d-flex justify-content-center align-items-center my-auto">
    <div id="payment-successful" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-lg czm-payment-success-modal">
            <div class="modal-content">
                <button type="button" class="btn-close mt-10 ml-10 text-end" data-bs-dismiss="modal"></button>
                <div class="modal-body px-10 pt-50 pb-50">
                    <div class="d-flex justify-content-center mb-20">
                        <img src="{{ asset('images/subscription-success.png') }}" alt="Subscription Successful">
                    </div>
                    <div class="text-center">
                        <h3 class="text-center czm-primary-text">Thank you for your subscription!</h3>
                        <p class="text-dark">Click here to return to homepage.</p>
                    </div>
                    <a href="{{ route('home') }}" class="d-flex justify-content-center">
                        <button class="border shadow text-white w-75 py-3 czm-primary-bg" type="submit">
                            Back to Home
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
