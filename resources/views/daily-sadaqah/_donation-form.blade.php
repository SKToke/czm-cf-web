<div class="donation-form-outer py-0">
    <form action="{{ route('payment.ajax') }}" method="post">
        @csrf
        <div class="p-3">
            {{-- FREQUENCY --}}
            <div class="mb-3">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button"
                                class="btn btn-success w-100 freq-btn active"
                                data-value="daily">
                            Daily
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button"
                                class="btn btn-outline-secondary w-100 freq-btn"
                                data-value="monthly">
                            Monthly
                        </button>
                    </div>
                </div>
                <input type="hidden" name="frequency" id="frequency" value="daily">
            </div>
            {{-- AMOUNT PRESETS --}}
            <div class="mb-3">
                <div class="row g-2" id="amount-buttons">
                    {{-- JS will inject buttons --}}
                </div>
            </div>
            {{-- AMOUNT --}}
            <div class="form-group mb-3">
                <div class="field-label">Amount<span class="text-danger">*</span> *</div>
                <input type="text"
                       id="amount"
                       name="payment-amount"
                       class="form-control"
                       value="20">
            </div>
            {{-- NAME --}}
            <div class="form-group mb-3">
                <div class="field-label">Name</div>
                <input type="text"
                       name="payment-name"
                       id="name"
                       class="form-control">
            </div>
            {{-- Email --}}
            <div class="form-group mb-3">
                <div class="field-label">Email</div>
                <input type="text"
                       name="payment-email"
                       id="email"
                       class="form-control">
            </div>
            {{-- PHONE --}}
            <div class="form-group mb-3">
                <div class="field-label">Mobile</div>
                <input type="text"
                       name="payment-phone"
                       id="phone"
                       class="form-control">
            </div>
            {{-- PAYMENT METHOD --}}
            <div class="form-group mb-3">
                <div class="field-label">Payment Method</div>
                <div class="d-flex gap-4 mt-2">
                    <label class="d-flex align-items-center gap-2">
                        <input type="radio"
                               name="payment_method"
                               value="bkash"
                               checked>
                        <img src="{{ asset('images/payment-logo/bkash-icon-logo.svg') }}"
                             width="28"
                             alt="bKash">
                        bKash
                    </label>
                    <label class="d-flex align-items-center gap-2">
                        <input type="radio"
                               name="payment_method"
                               value="card">
                        <img src="{{ asset('images/payment-logo/visa-master.png') }}"
                             width="40"
                             alt="Visa Master">
                        Visa / Master
                    </label>
                </div>
            </div>
            {{-- PAYMENT BUTTON --}}
            <button id="sslczPayBtn"
                    class="border shadow text-white w-100 py-3 czm-primary-bg mt_30"
                    token=""
                    postdata=""
                    endpoint="{{ url('/pay-via-ajax') }}">
                Pay Now
            </button>
        </div>
    </form>
    {{-- NOTICE --}}
    <div class="p-3 mb-3 rounded bg-light text-center text-dark">
        নিয়মিত অনুদান সংক্রান্ত যেকোনো বিষয় বুঝতে অসুবিধা হলে —
        autopay@czm-bd.org
    </div>
</div>
