<x-main>
    <h3 class="my-4 d-flex justify-content-center fw-bold czm-primary-text">
        {{ $currentUser->getFullNameAttribute() }}'s Donations <br class="d-block d-md-none">
        (Total: BDT {{ $currentUser->getTotalDonationAmount() }})
    </h3>
    <div class="container">
        <nav class="donation-tabs nav nav-pills flex-column flex-sm-row">
            <a href="{{ route('donation-history') }}" class="flex-sm-fill text-sm-center nav-link active"
               id="past-donation-tab">
                <i class="fa-solid fa-download me-2"></i> Past Donations
            </a>
            <a href="{{ route('upcoming-donations') }}" class="flex-sm-fill text-sm-center nav-link"
               id="upcoming-donation-tab">
                <i class="fa-solid fa-upload me-2"></i> Upcoming Donations
            </a>
        </nav>
    </div>

    <div id="donation_tab_content" class="mt-20">
        @include('user_donations.donation-history', [
            'pastDonations' => $pastDonations,
            'upcomingDonations' => $upcomingDonations,
            'currentUser' => $currentUser
            ]
        )
    </div>

    <script src="{{ asset('js/donation_tabs.js') }}"></script>
</x-main>
