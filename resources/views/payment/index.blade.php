<x-main>
    @if(request()->has('confirmation') && request()->query('confirmation') == 'success')
        @include('payment.payment-successful')
    @endif
    @include('payment.payment-form')
    @include('payment.other-payment-info')
</x-main>
