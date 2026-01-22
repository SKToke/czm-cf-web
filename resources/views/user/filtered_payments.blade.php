@php
    use \App\Models\Campaign;
@endphp

@if($payments && $payments->count() > 0)
    @foreach($payments as $payment)
        @php
            $program = null;
            $campaign = null;
            $type = 'general';
            if ($payment->campaign_id && $payment->campaign) {
                $campaign =  $payment->campaign;
                $program = $payment->campaign->program;
                $type = 'usual';
            } elseif ($payment->campaign_id && !$payment->campaign) {
                $campaign = Campaign::withTrashed()->where('id', $payment->campaign_id)->first();
                $program = $campaign->program;
                $type = 'deleted';
            }
        @endphp
        @include('user.payment_card', ['payment' => $payment, 'campaign' => $campaign, 'program' => $program, 'type' => $type])
    @endforeach
@else
    <div class="col-sm-12 text-center mb-4">
        <p>No records found.</p>
    </div>
@endif
