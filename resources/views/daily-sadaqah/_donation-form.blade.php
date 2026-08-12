<div class="donation-form-outer py-0">
    <form action="{{ route('daily-sadaqah.store') }}" method="post">
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
                <div class="field-label">Amount<span class="text-danger">*</span></div>
                <input type="text"
                       id="amount"
                       name="payment_amount"
                       class="form-control"
                       value="20">
            </div>
            @auth
                {{-- Logged-in user --}}
                <input type="hidden" name="payment_name" value="{{ auth()->user()->getFullNameAttribute() }}">
                <input type="hidden" name="payment_email" value="{{ auth()->user()->email }}">
                <input type="hidden" name="payment_phone" value="{{ auth()->user()->mobile_no }}">

                <div class="p-3 mb-3 rounded bg-light text-dark">
                    Donating as <strong>{{ auth()->user()->getFullNameAttribute() }}</strong>
                </div>
            @else
                {{-- NAME --}}
                <div class="form-group mb-3">
                    <div class="field-label">Name<span class="text-danger">*</span></div>
                    <input type="text"
                           name="payment_name"
                           id="name" required
                           class="form-control">
                </div>
                {{-- Email --}}
                <div class="form-group mb-3">
                    <div class="field-label">Email<span class="text-danger">*</span></div>
                    <input type="text"
                           name="payment_email"
                           id="email" required
                           class="form-control">
                </div>
                {{-- PHONE --}}
                <div class="form-group mb-3">
                    <div class="field-label">Mobile</div>
                    <input type="text"
                           name="payment_phone"
                           id="phone"
                           class="form-control">
                </div>
            @endauth
            @php
                $showBkashTest = request()->has('test_bkash') || old('test_bkash') || config('bkash.show_option', false);
            @endphp

            @if($showBkashTest)
                <input type="hidden" name="test_bkash" value="1">
            @endif

            {{-- PAYMENT METHOD --}}
            <div class="form-group mb-3">
                <div class="field-label">Payment Method</div>
                <div class="d-flex gap-4 mt-2">

                    @if($showBkashTest)
                    {{-- BKASH --}}
                    <span class="czm-radio-option-container">
                        <input type="radio"
                               class="czm-radio-btn"
                               id="bkash"
                               name="payment_method"
                               value="bkash"
                               {{ old('payment_method', 'bkash') === 'bkash' ? 'checked' : '' }}
                        >
                        <label class="czm-payment-radio-label d-flex align-items-center gap-2"
                               for="bkash">
                            <img src="{{ asset('images/payment-logo/bkash-icon-logo.svg') }}"
                                 width="25"
                                 alt="bKash">
                            bKash
                        </label>
                    </span>
                    @endif

                    {{-- VISA --}}
                    <span class="czm-radio-option-container">
                        <input type="radio"
                               class="czm-radio-btn"
                               id="card"
                               name="payment_method"
                               value="card"
                               {{ !$showBkashTest || old('payment_method') === 'card' ? 'checked' : '' }}
                        >
                        <label class="czm-payment-radio-label d-flex align-items-center gap-2"
                               for="card">
                            <img src="{{ asset('images/payment-logo/card-icon-logo.svg') }}"
                                 width="28"
                                 alt="Card">
                            Visa / Master
                        </label>
                    </span>
                </div>
            </div>


            {{-- PAYMENT BUTTON --}}
            <button id="payBtn" type="submit"
                    class="border shadow text-white w-100 py-3 czm-primary-bg mt_30">
                Pay Now
            </button>
        </div>
    </form>
    {{-- NOTICE --}}
    <div class="p-3 mb-3 rounded bg-light text-center text-dark">
        নিয়মিত অনুদান সংক্রান্ত যেকোনো প্রয়োজনে —
        donation@czm-bd.org / 01729 296 296
    </div>
</div>
