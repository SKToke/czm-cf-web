<div class="card custom-dashboard-top-card" style="width: 18rem;">
    <a href="{{ env('APP_ADMIN_URL') . '/recurring-subscriptions' }}">
        <div class="card-body">
            <h5 class="card-title">R. Subscriptions </h5>
            <p class="card-text">{{ $subscriptions }}</p>
        </div>
    </a>
</div>
