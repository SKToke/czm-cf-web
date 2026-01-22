<div class="container mb-4 hoverable-card">
    <div class="card shadow-md py-2">
        <div class="row card-body py-0">
            <div class="col-8 my-auto">
                <h5 class="fw-bold my-0 text-start">{{ $donation->getDonationType() }}</h5>
            </div>
            <div class="col-4">
                <div class="h-100 d-flex flex-column justify-content-between align-items-end">
                    <div class="d-flex flex-column align-items-end w-100">
                        <span class="font-weight-bold fs-5">BDT&nbsp;{{ $donation->amount }}</span>
                        <p class="color mb-0">{{ $donation->getFormattedDonationDate() }}</p>
                        <p class="color mb-0">{{ $donation->getFormattedDonationTime() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
