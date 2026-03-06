<x-main>

    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">
        Your Daily Sadaqah Subscriptions
    </h3>

    <div class="container">

        <div class="row">

            <table class="table table-bordered text-center">

                <thead class="table-light">

                <tr>
                    <th>Amount</th>
                    <th>Frequency</th>
                    <th>Status</th>
                    <th>Started</th>
                    <th>Next Billing</th>
                    <th>Actions</th>
                </tr>

                </thead>

                <tbody>

                @foreach($subscriptions as $subscription)

                    <tr>

                        <td>{{ $subscription->amount }} BDT</td>

                        <td>{{ ucfirst($subscription->frequency_type) }}</td>

                        <td>
<span class="badge
@if($subscription->status == 'active') bg-success
@elseif($subscription->status == 'paused') bg-warning
@else bg-secondary
@endif">
{{ ucfirst($subscription->status) }}
</span>
                        </td>

                        <td>{{ $subscription->started_at ? \Carbon\Carbon::parse($subscription->started_at)->format('Y-m-d') : $subscription->started_at }}</td>

                        <td>{{ $subscription->next_billing_at ? \Carbon\Carbon::parse($subscription->next_billing_at)->format('Y-m-d') : $subscription->next_billing_at }}</td>

                        <td>

                            @if($subscription->status == 'active')

                                <form method="POST" action="{{ route('user-daily-sadaqah.pause',$subscription->id) }}"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-warning">Pause</button>
                                </form>

                                <form method="POST" action="{{ route('user-daily-sadaqah.cancel',$subscription->id) }}"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Cancel</button>
                                </form>

                            @endif

                            @if($subscription->status == 'paused')

                                <form method="POST" action="{{ route('user-daily-sadaqah.resume',$subscription->id) }}"
                                      class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Resume</button>
                                </form>

                            @endif

                            <a href="{{ route('user-daily-sadaqah.transactions',$subscription->id) }}"
                               class="btn btn-sm btn-primary">
                                Transactions
                            </a>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</x-main>
