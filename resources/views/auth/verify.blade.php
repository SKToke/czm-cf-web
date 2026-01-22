<x-main>
<div class="container mt-4 mb-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card user-verification-card">
                <div class="card-header verification-header">{{ __('Verify Your Email Address') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('A fresh verification link has been sent to your email address.') }}
                        </div>
                    @endif
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    <br>
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                    <div class="mt-4">
                        <p>Want to return back to the home page?</p>
                        <a class="btn btn-sm home-btn" href="{{ route('home') }}" role="button">Go to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-main>
