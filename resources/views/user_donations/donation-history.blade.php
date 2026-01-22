<div class="container mb-3 czm-secondary-bg py-2">
    <div class="row justify-content-around czm-amount-counter text-white fw">
            <span class="col-4 text-center">Total Zakat: <br class="d-block d-md-none">
                {{ $currentUser->getTotalZakatAmount() }}&nbsp;(BDT)
            </span>
        <span class="col-4 text-center">Total Sadakah: <br class="d-block d-md-none">
                {{ $currentUser->getTotalSadakahAmount() }}&nbsp;(BDT)
            </span>
        <span class="col-4 text-center">Total Waqf: <br class="d-block d-md-none">
                {{ $currentUser->getTotalWaqfAmount() }}&nbsp;(BDT)
            </span>
    </div>
</div>
<div class="text-dark">
    @foreach($pastDonations ?? [] as $donation)
        @include('user_donations.'
        . ($donation->isGeneralDonation() ? 'general' : 'campaign')
        . '-donation-card',['donation' => $donation])
    @endforeach
    @if(!$pastDonations)
        <h5 class="fst-italic text-center mt-100 mb-20">No donations have been made yet</h5>
        <div class="d-flex align-items-center justify-content-center mb-100">
            <a class="czm-primary-btn inverse" href="{{ route('payment.index') }}">Donate Now</a>
        </div>
    @endif
</div>
