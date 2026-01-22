<div class="container-fluid w-75 my-3">
    <div class="text-dark d-flex justify-content-center text-center czm-donation-declaration">
        Center for Zakat Management (CZM) has been implementing a set of comprehensive programs
        focused on the income generation, livelihood development, education, primarily healthcare
        and emergency assistance. For the past 12 years, CZM services impacted more than
        10,00,000 needy people under different programs.
    </div>
    <div class="other-payment-container">
        <div class="row text-dark">
            <div class="col-lg-6">
                @include('payment.bank-details')
            </div>
            <div class="d-lg-flex d-none col-lg-1 justify-content-center align-self-center" style="height: 200px;">
                <div class="vr"></div>
            </div>
            <div class="col-lg-5">
                @include('payment.online-payment-details')
            </div>
        </div>
    </div>
</div>
