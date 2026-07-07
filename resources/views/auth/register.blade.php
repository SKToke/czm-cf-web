<div class="row devise-modal">
    <div class="col-5 modal-img d-none d-md-block">
        <img src="{{ asset('images/czm_auth_modal.png') }}" alt="Modal Image">
    </div>
    <div class="col-12 col-md-7 pt-4 pb-4">
        <div class="row">
            <div class="col-11">
                <h5 class="text-center mt-4">Create an account</h5>
            </div>
            <div class="col-1">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
        <div class="modal-body">
            <form id="registrationForm" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="field form-group mb-3">
                            <label for="first_name" class="mb-2">Name*</label>
                            <br>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                            @if(isset($errors) && $errors->any())
                            @error('first_name')
                            <span class="text-danger">{{ $message }}</span>
                            @enderror
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="email" class="mb-2">Email*</label>
                    <br>
                    <input type="email" name="email" class="form-control" autocomplete="email" value="{{ old('email') }}" required>
                    @if(isset($errors) && $errors->any())
                        @error('email')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    @endif
                </div>

                <div class="field form-group mb-3">
                    <label for="mobile_no" class="mb-2">Mobile Number</label>
                    <br>
                    <input type="text" name="mobile_no" class="form-control" pattern="[0-9]*" title="Only numeric values." value="{{ old('mobile_no') }}">
                    @if(isset($errors) && $errors->any())
                    @error('mobile_no')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @endif
                </div>

                <div class="field form-group mb-3">
                    <label for="password" class="mb-2">Password*(6 characters minimum)</label>
                    <br>
                    <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                    @if(isset($errors) && $errors->any())
                    @error('password')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @endif
                </div>

                <div class="field form-group mb-3">
                    <label for="password_confirmation" class="mb-2">Confirm Password*</label>
                    <br>
                    <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                    @if(isset($errors) && $errors->any())
                    @error('password_confirmation')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @endif
                </div>

                <div class="field form-group mb-3" style="display: none !important;">
                    <label for="website">Website</label>
                    <input type="text" name="website" id="website" autocomplete="off" tabindex="-1">
                </div>

                <!-- Stateless Math Captcha Challenge -->
                @php
                    $num1 = rand(1, 9);
                    $num2 = rand(1, 9);
                    $operator = rand(0, 1) ? '+' : '-';
                    if ($operator === '-') {
                        if ($num1 < $num2) {
                            $temp = $num1;
                            $num1 = $num2;
                            $num2 = $temp;
                        }
                        $answer = $num1 - $num2;
                    } else {
                        $answer = $num1 + $num2;
                    }
                    $captchaHash = encrypt($answer);
                @endphp

                <div class="field form-group mb-3">
                    <label for="captcha_answer" class="mb-2">Security Question: What is {{ $num1 }} {{ $operator }} {{ $num2 }}?*</label>
                    <br>
                    <input type="hidden" name="captcha_hash" value="{{ $captchaHash }}">
                    <input type="text" name="captcha_answer" class="form-control" placeholder="Enter answer" required>
                    @if(isset($errors) && $errors->any())
                    @error('captcha_answer')
                    <span class="text-danger">{{ $message }}</span>
                    @enderror
                    @endif
                </div>


                <div class="actions mb-3 btn-devise-submit">
                    <button type="submit" class="btn btn-primary">Sign up</button>
                </div>
                <div class="text-center mt-2">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal" id="require-login">Already have an account? Log in</a>
                </div>
            </form>

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
