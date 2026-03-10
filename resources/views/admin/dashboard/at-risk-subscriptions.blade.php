<div class="card custom-dashboard-top-card" style="width: 16rem;">
    <a href="{{ env('APP_ADMIN_URL') . '/recurring-subscriptions' }}">
        <div class="card-body">
            <h5 class="card-title">Risk Subscriptions </h5>
            <p class="card-text">{{ $atRiskSubscriptions }}</p>
        </div>
    </a>
</div>
