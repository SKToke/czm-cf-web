<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="card top-line-card">
    <div class="card-body">
        <h5 class="card-title">You may also want to visit</h5>
    </div>
</div>
<div class="row">
    <div class="col">
        <div class="card extra-visit-card" style="width: 20rem;">
            <a href="{{ env('APP_ADMIN_URL') . '/reports' }}">
                <div class="card-body">
                    <h5 class="card-title">Reports</h5>
                    <p class="card-text">
                        10 different reports.
                    </p>
                    <p class="card-text">
                        Download updated data as reports on 10 different categories.
                    </p>
                </div>
            </a>
        </div>
    </div>
    <div class="col">
        <div class="card extra-visit-card" style="width: 20rem;">
            <a href="{{ env('APP_ADMIN_URL') . '/user-zakat-calculations' }}">
                <div class="card-body">
                    <h5 class="card-title">User Zakat Calculation records</h5>
                    <p class="card-text">
                        {{ $zakatCalculations }} zakat-calculations are recorded.
                    </p>
                    <p class="card-text">
                        See zakat-calculation details of individuals.
                    </p>
                </div>
            </a>
        </div>
    </div>
    <div class="col">
        <div class="card extra-visit-card" style="width: 20rem;">
            <a href="{{ env('APP_ADMIN_URL') . '/nisabs' }}">
                <div class="card-body">
                    <h5 class="card-title">Nisab Chart</h5>
                    <p class="card-text mb-0">
                        Gold value (per gram): {{ $goldValue }} Tk
                    </p>
                    <p class="card-text mt-0 pt-0">
                        Silver value (per gram): {{ $silverValue }} Tk
                    </p>
                    <p class="card-text">
                        Last updated on: {{ $nisabUpdateDate }}
                    </p>
                </div>
            </a>
        </div>
    </div>
    <div class="col">
        <div class="card extra-visit-card" style="width: 20rem;">
            <a href="{{ env('APP_ADMIN_URL') . '/contact-us-queries' }}">
                <div class="card-body">
                    <h5 class="card-title">Contact-us queries</h5>
                    <p class="card-text">
                        {{ $contactQueries }} queries aren't responded yet.
                    </p>
                    <p class="card-text">
                        View who wanted to contact CZM here.
                    </p>
                </div>
            </a>
        </div>
    </div>
    <div class="col">
        <div class="card extra-visit-card" style="width: 20rem;">
            <a href="{{ env('APP_ADMIN_URL') . '/notifications' }}">
                <div class="card-body">
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text">
                        {{ $notifications }} Notifications sent on last 10 days.
                    </p>
                    <p class="card-text">
                        Want to send some updates as notifications to your users?
                    </p>
                </div>
            </a>
        </div>
    </div>
</div>

