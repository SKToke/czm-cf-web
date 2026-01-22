@php
    use App\Models\Campaign;
@endphp
<x-main>
<h3 class="mt-3 d-flex justify-content-center fw-bold czm-primary-text mb-30">Subscription History</h3>
<section class="filter">
  <div class="container">
    <div class="row">
      @if ($subscriptions && $subscriptions->count() > 0)
        <div class="col-sm-12">
          @foreach ($subscriptions as $subscription)
              @if($subscription->campaign)
                    @include('user.campaign_subscription_history_card', ['subscription' => $subscription])
              @endif
          @endforeach
        </div>
        <div class="col-sm-12 pagination text-center">
          {{ $subscriptions->links() }}
        </div>
      @else
        <div class="col-sm-12 text-center mt-2 mb-4">
          <p>No subscriptions yet.</p>
        </div>
      @endif
    </div>
  </div>
</section>
</x-main>
