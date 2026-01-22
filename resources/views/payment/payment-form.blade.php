<div class="container-fluid mt-30">
    <div class="row d-flex justify-content-center align-items-center">
        <div class="col-lg-9">
            <div class="row">
                <div class="col-md-8 order-md-1 order-2">
                    <div class="donation-form-outer py-0">
                        <form action="{{ route('payment.ajax') }}" method="post">
                            @csrf
                            <div>
                                <h4 class="mt-md-2 mt-0 pt-4">
                                    Pay your Zakat / Sadaqah / Donation with Debit Card / Credit Card / Mobile Money
                                </h4>
                                <br class="d-none d-md-block">
                                <div class="row clearfix">
                                    <div class="form-group col-md-12 clearfix">
                                        <span class="czm-payment-radio-label fw-bolder">Donate As:</span>
                                        <br class="d-md-none d-block">
                                        <br class="d-md-none d-block">

                                        @if(!auth()->user())
                                        <span class="czm-radio-option-container p-10 ml-20">
                                            <input type="radio" checked class="czm-radio-btn"
                                                   id="donor-type-1" name="donor-type" value="1">
                                            <label class="czm-payment-radio-label" for="donor-type-1">Regular / Own</label>
                                        </span>
                                        @endif

                                        @if(!auth()->guest())
                                        <span class="czm-radio-option-container p-10 ml-20">
                                            <input type="radio" checked class="czm-radio-btn"
                                                   id="donor-type-2" name="donor-type" value="2">
                                            <label class="czm-payment-radio-label" for="donor-type-2">You</label>
                                        </span>
                                        @endif
                                        <br class="d-md-none d-block">
                                        <br class="d-md-none d-block">

                                        <span class="czm-radio-option-container p-10 ml-20">
                                            <input type="radio" class="czm-radio-btn"
                                                   id="donor-type-3" name="donor-type" value="3">
                                            <label class="czm-payment-radio-label" for="donor-type-3">Anonymous</label>
                                        </span>
                                    </div>
                                    <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                        <div class="field-label">Amount <span class="required">*</span></div>
                                        <input type="text" placeholder="Amount (BDT)" required name="payment-amount" id="amount"
                                            value="{{ request()->has('payableZakat') ? request('payableZakat') : null }}">
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12 payment-name-wrapper">
                                        <div class="field-label">Name <span class="required">*</span></div>
                                        <input type="text" placeholder="Name" required name="payment-name" id="name">
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12 payment-email-wrapper">
                                        <div class="field-label">Email <span class="required">*</span></div>
                                        <input type="email" placeholder="Email" required name="payment-email" id="email">
                                    </div>

                                    <div class="form-group col-lg-6 col-md-6 col-xs-12 payment-phone-wrapper">
                                        <div class="field-label">Phone</div>
                                        <input type="text" placeholder="+880"  name="payment-phone" id="phone">
                                    </div>
                                </div>
                                <br class="d-none d-md-block">
                                <div class="form-group col-xs-12 clearfix">
                                    <span class="czm-payment-radio-label fw-bolder">Select your donation type:</span>
                                    <br class="d-md-none d-block">
                                    <br class="d-md-none d-block">
                                    <span class="czm-radio-option-container p-10 ml-20">
                                        <input type="radio" {{ ((!request()->has('check-donation')) || (request()->query('check-donation')==false)) ? 'checked' : null }} class="czm-radio-btn"
                                               id="payment-type-1" name="payment-type" value="1">
                                        <label class="czm-payment-radio-label" for="payment-type-1">Zakat</label>
                                    </span>
                                    <span class="czm-radio-option-container p-10 ml-20">
                                        <input type="radio" {{ ((request()->has('check-donation')) && (request()->query('check-donation')==true)) ? 'checked' : null }} class="czm-radio-btn"
                                               id="payment-type-2" name="payment-type" value="2">
                                        <label class="czm-payment-radio-label" for="payment-type-2">Sadakah / Donation</label>
                                    </span>

                                    <span class="czm-radio-option-container p-10 ml-20">
                                        <input type="radio" {{ ((request()->has('check-donation')) && (request()->query('check-donation')==true)) ? 'checked' : null }} class="czm-radio-btn"
                                               id="payment-type-3" name="payment-type" value="3">
                                        <label class="czm-payment-radio-label" for="payment-type-3">Cash Waqf</label>
                                    </span>
                                </div>
                                <br class="d-none d-md-block">
                                 <span class="text-dark">
                                     <input type="checkbox" name="payment-agree" required/>
                                     <span class="ml-5">
                                         By clicking PAY NOW, you agree to our
                                         <a href="{{ route('terms-and-conditions') }}" target="_blank">
                                             terms, conditions and refund policy
                                         </a>
                                     </span>
                                 </span>

                                <button id="sslczPayBtn"
                                        class="border shadow text-white w-100 py-3 czm-primary-bg mt_30 mb_30"
                                        token=""
                                        postdata=""
                                        endpoint="{{ url('/pay-via-ajax') }}"> Pay Now
                                </button>

                            </div>
                            @if($campaign)
                                <input type='hidden' name="campaign-id" value="{{$campaign->campaign_id}}" class="">
                            @endif
                        </form>
                    </div>

                </div>
                <div class="col-md-4 order-md-2 order-1 payment-poster mb-30 pt-4">
                    @if($campaign)
                        @include('payment.campaign-card', ['campaign' => $campaign])
                    @else
                        <img src="{{ asset('images/payment_poster.jpg') }}" class="w-100" alt="Payment Poster">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var obj = {};

        // Helper function to get value if element exists
        function val(id) {
            var element = document.getElementById(id);
            return element ? element.value : null;
        }

        // Assign the first non-empty donor type value
        obj.donor_type = val('donor-type-1') || val('donor-type-2') || val('donor-type-3');

        // Directly assign values for amount, name, email, and phone
        obj.amount = val('amount');
        obj.name = val('name');
        obj.email = val('email');
        obj.phone = val('phone');

        // Assign the first non-empty payment type value
        obj.donation_type = val('payment-type-1') || val('payment-type-2')|| val('payment-type-3');

        // Setting postdata for the SSLCommerz button
        var sslczPayBtn = document.getElementById('sslczPayBtn');
        if (sslczPayBtn) {
            sslczPayBtn.setAttribute('postdata', JSON.stringify(obj));
        }
    });
</script>


