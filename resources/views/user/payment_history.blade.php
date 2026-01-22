<x-main>
    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">Your Payment History</h3>
    <div class="container">
        <div class="row">
            <div class="col-10 mt-1">
                <form id="filter-payments-form" action="{{ route('filter-payments') }}" data-payment-route="{{ route('user-payments') }}" method="GET">
                    <div class="row">
                        <div class="col-10">
                            <div class="text-start">
                                <span class="me-4">Payment Statement Range:</span>
                                <label for="start_date" class="d-inline-block">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="d-inline-block custom-date-field" data-target="payment-filter.selectStartDate" placeholder="mm/dd/yyyy">

                                <label for="end_date" class="d-inline-block">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="d-inline-block custom-date-field" data-target="payment-filter.selectEndDate" placeholder="mm/dd/yyyy">
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="text-end">
                                <button type="button" id="reset-filter-btn" class="btn btn-sm reset-btn btn-outline-secondary" data-target="payment-filter.clearButton">Reset Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-2">
                <div class="row text-center">
                    <form action="{{ route('user.export-payment-statement') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_start_date" id="payment-start-date" value="">
                        <input type="hidden" name="payment_end_date" id="payment-end-date" value="">

                        <div class="button-group d-flex justify-content-center align-items-center flex-wrap">
                            @if (Auth::check() && count($payments ?? []) > 0)
                                <button type="submit" class="calculate-btn export-payment mr-15">Export as PDF</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row mb-4 mt-4" id="paymentsContainer">
            @include('user.filtered_payments', ['payments' => $payments])
        </div>

    </div>
    <script src="{{ asset('js/filter_payments.js') }}"></script>
</x-main>
