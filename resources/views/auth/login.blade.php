
<div class="row devise-modal">
    <div class="col-5 modal-img d-none d-md-block">
        <img src="{{ asset('images/czm_auth_modal.png') }}" alt="Modal Image">
    </div>
    <div class="col-12 col-md-7 pt-4 pb-4">
        <div class="row">
            <div class="col-11">
                <h5 class="text-center mt-4">Log In</h5>
            </div>
            <div class="col-1">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
        <div class="modal-body">
            <form id="loginForm" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="field form-group mb-3">
                    <label for="email" class="mb-2">Email*</label><br>
                    <input type="email" name="email" class="form-control" required autocomplete="email" value="{{ old('email') }}">
                </div>

                <div class="field form-group mb-3">
                    <label for="password" class="mb-2">Password*</label><br>
                    <input type="password" name="password" class="form-control" required autocomplete="current-password">
                </div>

                @if(Route::has('password.request'))
                    <div class="field form-group mb-2">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Remember Me</label>
                    </div>
                @endif

                <div class="mb-3">
                    <a href="{{ route('forget.password.get') }}">Forgot Password?</a>
                </div>

                <div class="actions mb-3 btn-devise-submit">
                    <button type="submit" class="btn btn-primary" >Log in</button>
                </div>
                <div class="text-center mt-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#registerModal" data-bs-dismiss="modal" id="require-register">Don't have an account? Register</a>
                </div>
            </form>

            <br>

            <div class="line"></div>
            <div class="social-btn">
                <a href="{{ route('social.login','google') }}"
                    class="btn google-btn"
                >
                    <img src="{{ asset('images/google.png') }}" class="icon-image" alt="Google">
                    <span class="button-text">Continue with Google</span>
                </a>
                <a href="{{ route('social.login','facebook') }}"
                    class="btn facebook-btn"
                >
                    <i class="fa-brands fa-square-facebook"></i>
                    <span class="button-text">Continue with Facebook</span>
                </a>
            </div>
        </div>
    </div>
</div>
