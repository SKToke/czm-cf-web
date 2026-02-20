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
        <a href="tel:+880 22222 98 255" class="text-black header-info-text">
            +880 22222 98 255
        </a>
    </div>

    <!-- Address Section (Visible on medium and larger screens) -->
    <div class=" d-none d-md-flex align-items-center hide-on-ipad" style="width: 20%;">
        <i class="fa-solid fa-location-dot fa-2x czm-primary-text me-2"></i>
        <p class="long-text text-black mb-0 header-info-text">
            1st Floor, 113/B, Tejgaon Industrial Area, Dhaka-1208
        </p>
    </div>

    <!-- Buttons Section -->
    <div class="d-flex  align-items-center justify-content-md-between justify-content-between calc-width">
        <a class="czm-primary-btn me-2 d-md-flex d-none"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false" href="{{ route('payment.index', ['check-donation' => true]) }}">
            Calculate Zakat
        </a>
        <ul class="dropdown-menu zakat-header" aria-labelledby="dropdownMenuButton1">
            <li><a class="dropdown-item" href="{{ route('zakat-calculator') }}">Zakat Calculator</a></li>
            <hr>
            <li><a class="dropdown-item" href="{{ route('zakat-calculator') }}">Nisab value</a></li>
            <hr>
            <li><a class="dropdown-item" href="{{ route('daily-sadaqah') }}">Daily Sadaqah</a></li>
        </ul>
        <a class="czm-secondary-btn d-flex" href="{{ route('payment.index', ['check-donation' => true]) }}">
            Donate Now
        </a>
    </div>


    <!-- Auth Section -->
    <div class="d-flex align-items-center justify-content-end auth-width">
        <x-auth/>
    </div>

</div>
