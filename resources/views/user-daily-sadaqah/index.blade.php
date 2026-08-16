<x-main>

    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">
        Your Recurring Sadaqah Subscriptions
    </h3>

    <div class="container">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">

            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle">

                    <thead class="table-light">

                    <tr>
                        <th>Gateway</th>
                        <th>Amount & Frequency</th>
                        <th>Account / Card</th>
                        <th>Status</th>
                        <th>Started</th>
                        <th>Next Billing</th>
                        <th>Total Donated</th>
                        <th>Actions</th>
                    </tr>

                    </thead>

                    <tbody>

                    @forelse($subscriptions as $subscription)

                        <tr>
                            <td>
                                @if($subscription->payment_gateway === 'bkash')
                                    <span class="badge" style="background-color: #e2136e; color: #fff; font-size: 0.85rem; padding: 5px 10px;">
                                        bKash
                                    </span>
                                @else
                                    <span class="badge" style="background-color: #004a98; color: #fff; font-size: 0.85rem; padding: 5px 10px;">
                                        SSLCommerz
                                    </span>
                                @endif
                            </td>

                            <td>
                                <strong>{{ number_format($subscription->amount, 2) }} {{ $subscription->currency ?? 'BDT' }}</strong><br>
                                <span class="text-muted small">{{ ucfirst($subscription->frequency_type) }}</span>
                            </td>

                            <td>
                                @if($subscription->payment_gateway === 'bkash')
                                    <span class="text-muted small">bKash Recurring Wallet</span>
                                @else
                                    {{ $subscription->card_issuer_bank ?: 'Card Payment' }}<br>
                                    <span class="text-muted small">{{ $subscription->card_no ? '****' . substr($subscription->card_no, -4) : '' }}</span>
                                @endif
                            </td>

                            <td>
                                <span class="badge
                                    @if($subscription->status == 'active') bg-success
                                    @elseif($subscription->status == 'paused') bg-warning text-dark
                                    @elseif($subscription->status == 'cancelled') bg-danger
                                    @elseif($subscription->status == 'expired') bg-secondary
                                    @else bg-secondary
                                    @endif">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>

                            <td>{{ $subscription->started_at ? \Carbon\Carbon::parse($subscription->started_at)->format('Y-m-d') : '-' }}</td>

                            <td>{{ $subscription->next_billing_at ? \Carbon\Carbon::parse($subscription->next_billing_at)->format('Y-m-d') : '-' }}</td>

                            <td><strong>{{ number_format($subscription->transactions_sum_amount ?? 0, 2) }} {{ $subscription->currency ?? 'BDT' }}</strong></td>
                            
                            <td>
                                <div class="d-flex justify-content-center gap-1">

                                    {{-- Pause action: Only available for SSLCommerz --}}
                                    @if($subscription->status == 'active' && $subscription->payment_gateway !== 'bkash')
                                        <form method="POST" action="{{ route('user-daily-sadaqah.pause', $subscription->id) }}"
                                              onsubmit="return confirm('Are you sure you want to pause this subscription?');"
                                              class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" title="Pause auto-billing">Pause</button>
                                        </form>
                                    @endif

                                    {{-- Resume action: Only available for SSLCommerz --}}
                                    @if($subscription->status == 'paused' && $subscription->payment_gateway !== 'bkash')
                                        <form method="POST" action="{{ route('user-daily-sadaqah.resume', $subscription->id) }}"
                                              class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success" title="Resume auto-billing">Resume</button>
                                        </form>
                                    @endif

                                    {{-- Cancel action: Available for all active/paused subscriptions --}}
                                    @if(in_array($subscription->status, ['active', 'paused', 'initiated']))
                                        <form method="POST" action="{{ route('user-daily-sadaqah.cancel', $subscription->id) }}"
                                              onsubmit="return confirm('Are you sure you want to cancel this recurring subscription?');"
                                              class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" title="Cancel subscription">Cancel</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('user-daily-sadaqah.transactions', $subscription->id) }}"
                                       class="btn btn-sm btn-primary">
                                        History
                                    </a>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="8" class="py-4 text-muted">
                                You do not have any active recurring subscriptions yet.
                            </td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>
            </div>

        </div>

    </div>

</x-main>

