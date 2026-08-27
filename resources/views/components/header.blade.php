<div class="row mx-auto czm-header-container container" style="height: 86px;width: 100%">

    <!-- Logo Section -->
    <div class="d-flex align-items-center justify-content-end custom-width">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/czm_logo.png') }}" alt="Center for Zakat Management" class="czm-header-logo"/>
        </a>
    </div>

    <!-- Email Section (Visible on medium and larger screens) -->
    <div class="d-none d-md-flex align-items-center hide-on-ipad" style="width: 15%;">
        <i class="fa-solid fa-envelope-open-text fa-2x czm-primary-text me-2"></i>
        <a href="mailto:info@czm-bd.org" class="text-black header-info-text">
            info@czm-bd.org
        </a>
    </div>

    <!-- Phone Section (Visible on medium and larger screens) -->
    <div class="d-none d-md-flex align-items-center hide-on-ipad" style="width: 20%;">
        <i class="fa-solid fa-phone-volume fa-2x czm-primary-text me-2"></i>
        <a href="tel:+88028870770" class="text-black header-info-text">
            +880 288 70 770
        </a>
    </div>

    <!-- Address Section (Visible on medium and larger screens) -->
    <div class=" d-none d-md-flex align-items-center hide-on-ipad" style="width: 20%;">
        <i class="fa-solid fa-location-dot fa-2x czm-primary-text me-2"></i>
        <p class="long-text text-black mb-0 header-info-text">
            1st Floor, 113/B, Tejgaon Industrial Area, Dhaka-1208
        </p>
    </div>

    <!-- Address Section (Visible on medium and larger screens) -->
    <div class=" d-none d-md-flex align-items-center hide-on-ipad" style="width: 15%;">
        <a class="czm-info-btn d-flex" href="{{ route('daily-sadaqah.index') }}">
            Daily Sadaqah
        </a>
    </div>

    <!-- Address Section (Visible on medium and larger screens) -->
    <div class=" d-none d-md-flex align-items-center hide-on-ipad" style="width: 15%;">
        <a class="czm-secondary-btn d-flex" href="{{ route('payment.index', ['check-donation' => true]) }}">
            Donate Now
        </a>
    </div>

    <!-- Auth Section -->
    <div class="d-flex align-items-center justify-content-end auth-width">
        <x-auth/>
    </div>

</div>

<!-- Mobile Action Buttons Bar (Design B: Only visible on mobile < md) -->
<div class="czm-mobile-action-bar d-flex d-md-none px-3 py-2">
    <a class="czm-mobile-btn czm-mobile-info-btn flex-fill me-2 text-center" href="{{ route('daily-sadaqah.index') }}">
        <i class="fa-solid fa-hand-holding-heart me-1"></i> Daily Sadaqah
    </a>
    <a class="czm-mobile-btn czm-mobile-secondary-btn flex-fill text-center" href="{{ route('payment.index', ['check-donation' => true]) }}">
        <i class="fa-solid fa-heart me-1"></i> Donate Now
    </a>
</div>

