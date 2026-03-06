<x-main>

    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">
        Subscription Payment History
    </h3>

    <div class="container">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('user-daily-sadaqah.index') }}"
                   class="btn btn-sm btn-outline-secondary">
                    ← Back
                </a>
            </div>
        </div>
        {{-- Subscription Summary --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="p-3 rounded bg-light text-dark">
                    <div class="row text-center">

                        <div class="col-md-3">
                            <strong>Amount</strong><br>
                            {{ $subscription->amount }} BDT
                        </div>

                        <div class="col-md-3">
                            <strong>Frequency</strong><br>
                            {{ ucfirst($subscription->frequency_type) }}
                        </div>

                        <div class="col-md-3">
                            <strong>Status</strong><br>
                            <span class="badge
                            @if($subscription->status=='active') bg-success
                            @elseif($subscription->status=='paused') bg-warning
                            @else bg-secondary
                            @endif">
                            {{ ucfirst($subscription->status) }}
                        </span>
                        </div>

                        <div class="col-md-3">
                            <strong>Next Billing</strong><br>
                            {{ optional($subscription->next_billing_at)->format('Y-m-d') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="row">

            <div class="col-12">

                <table class="table table-bordered table-striped text-center">

                    <thead class="table-light">

                    <tr>
                        <th>Transaction ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>

                    </thead>

                    <tbody>

                    @forelse($transactions as $txn)

                        <tr>

                            <td>{{ $txn->tran_id }}</td>

                            <td>{{ $txn->amount }} {{ $txn->currency }}</td>

                            <td>
                            <span class="badge
                                @if($txn->payment_status == 'valid') bg-success
                                @else bg-danger
                                @endif">
                                {{ ucfirst($txn->payment_status) }}
                            </span>
                            </td>

                            <td>{{ optional($txn->paid_at)->format('Y-m-d H:i') }}</td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="4">No transactions found.</td>
                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-main>
