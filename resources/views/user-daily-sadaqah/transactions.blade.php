<x-main>

    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">
        Subscription Payment History
    </h3>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <a href="{{ route('user-daily-sadaqah.index') }}"
               class="btn btn-sm btn-outline-secondary">
                ← Back to Subscriptions
            </a>
        </div>

        {{-- Subscription Summary --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-3 rounded bg-light text-dark border">
                    <div class="row text-center align-items-center">

                        <div class="col-md-2">
                            <strong>Gateway</strong><br>
                            @if($subscription->payment_gateway === 'bkash')
                                <span class="badge" style="background-color: #e2136e; color: #fff;">bKash</span>
                            @else
                                <span class="badge" style="background-color: #004a98; color: #fff;">SSLCommerz</span>
                            @endif
                        </div>

                        <div class="col-md-2">
                            <strong>Amount</strong><br>
                            {{ number_format($subscription->amount, 2) }} {{ $subscription->currency ?? 'BDT' }}
                        </div>

                        <div class="col-md-2">
                            <strong>Frequency</strong><br>
                            {{ ucfirst($subscription->frequency_type) }}
                        </div>

                        <div class="col-md-2">
                            <strong>Status</strong><br>
                            <span class="badge
                                @if($subscription->status=='active') bg-success
                                @elseif($subscription->status=='paused') bg-warning text-dark
                                @elseif($subscription->status=='cancelled') bg-danger
                                @else bg-secondary
                                @endif">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>

                        <div class="col-md-2">
                            <strong>Next Billing</strong><br>
                            {{ $subscription->next_billing_at ? \Carbon\Carbon::parse($subscription->next_billing_at)->format('Y-m-d') : '-' }}
                        </div>

                        <div class="col-md-2">
                            <strong>Total Donated</strong><br>
                            <strong>{{ number_format($transactions->whereIn('payment_status', ['valid', 'success'])->sum('amount'), 2) }} {{ $subscription->currency ?? 'BDT' }}</strong>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="row">

            <div class="col-12">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle">

                        <thead class="table-light">

                        <tr>
                            <th>#</th>
                            <th>Transaction ID / TrxID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date & Time</th>
                        </tr>

                        </thead>

                        <tbody>

                        @forelse($transactions as $index => $txn)

                            <tr>
                                <td>{{ $index + 1 }}</td>

                                <td>
                                    <code>{{ $txn->tran_id }}</code>
                                </td>

                                <td><strong>{{ number_format($txn->amount, 2) }} {{ $txn->currency ?? 'BDT' }}</strong></td>

                                <td>
                                    <span class="badge
                                        @if(in_array($txn->payment_status, ['valid', 'success'])) bg-success
                                        @elseif($txn->payment_status == 'refunded') bg-warning text-dark
                                        @else bg-danger
                                        @endif">
                                        @if(in_array($txn->payment_status, ['valid', 'success']))
                                            Success
                                        @elseif($txn->payment_status == 'refunded')
                                            Refunded
                                        @else
                                            Failed
                                        @endif
                                    </span>
                                </td>

                                <td>{{ optional($txn->paid_at)->format('Y-m-d h:i A') ?? '-' }}</td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="py-4 text-muted">No deduction transactions recorded yet.</td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>

</x-main>

