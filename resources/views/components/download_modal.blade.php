<div class="modal-header">
    <h5 class="modal-title ">Download</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="donor-info text-center">
        @if (!auth()->check())
            <p class="mt-5">Sign in before you proceed to download</p>
            <a class="btn btn-sm login-btn" id="require-login-button">Login</a>
            <div class="line"></div>
            <p class="mt-5">Give below information to download the file</p>
        @endif
    </div>
    <div class="subscription-form">
        <form method="POST" action="{{ route('download', ['id' => $publication->id]) }}">
            @csrf
            <div class="personal-info mt-50 mb-10">
                <div class="field-group mt-20">
                    <div class="row">
                        <div class="col-4">
                            <label for="name">
                                <span>*</span> Name
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="name" name="name" placeholder="Your name.." required class="custom-text-field">
                        </div>
                    </div>
                </div>
                <div class="field-group mt-20">
                    <div class="row">
                        <div class="col-4">
                            <label for="email">
                                <span>*</span> Email
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="email" id="email" name="email" placeholder="Your email address.." required class="custom-email-field">
                        </div>
                    </div>
                </div>
                <div class="field-group mt-20">
                    <div class="row">
                        <div class="col-4 pe-0">
                            <label for="mobile_no">
                                Contact Number
                            </label>
                        </div>
                        <div class="col-8">
                            <input type="text" id="mobile_no" name="mobile_no" placeholder="Your mobile number.." class="custom-text-field" pattern="[0-9]*" title="Only numeric values.">
                        </div>
                    </div>
                </div>
            </div>
            <div class="submit-btn text-center">
                <button type="submit" class="btn btn-sm subscribe-btn">Download</button>
            </div>
        </form>
    </div>
</div>


<!-- Login Modal -->
<div class="modal fade" id="login-modal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @include('auth.login')
            </div>
        </div>
    </div>
</div>
