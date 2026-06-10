<x-main>
    @if(request()->has('confirmation'))
        @include('payment.payment-status-modal')
    @endif
    @include('payment.payment-form')
    @include('payment.other-payment-info')
</x-main>
