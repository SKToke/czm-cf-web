@php
    use App\Enums\CampaignSubscriptionTypeEnum;
@endphp
<div class="modal fade" id="subscribeCampaignModal" tabindex="-1" role="dialog" aria-labelledby="subscribeCampaignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content subscribe-case-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="subscribeCampaignModalLabel">Support this campaign to donate on a regular basis</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if (!Auth::user())
                <div class="donor-info text-center">
                    <p class="mt-5">Sign in before you proceed subscription</p>
                    <a class="btn btn-sm login-btn" id="require-login-button">Log In</a>
                    <div class="line"></div>
                    <p class="mt-5">Support this campaign anonymously with the least information to get donation updates.</p>
                </div>
                @endif
                <div class="subscription-form">
                    <form action="{{ route('subscribe-campaign', ['slug' => $campaign->slug]) }}" method="POST">
                        @csrf
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                        <div class="subscription-info mb-50 text-center">
                            <label for="subscription_type">Choose how you want to donate</label>
                            <select name="subscription_type" id="subscription_type">
                                @foreach(CampaignSubscriptionTypeEnum::cases() as $case)
                                    <option value="{{ $case->value }}">{{ $case->getTitle() }}</option>
                                @endforeach
                            </select>
                            <br>
                            <label for="subscribed_amount" class="mt-20">Your donation amount: BDT
                                <input type="number" name="subscribed_amount" class="d-inline-block mt-20 custom-number-field" placeholder="Donation Amount" min="1" max="99999999" required>
                            </label>
                            <br>
                            <div class="line"></div>
                        </div>
                        <div class="personal-info mt-50 mb-10">
                            <div class="field-group mt-20">
                                <div class="row">
                                    <div class="col-4">
                                        <label for="name"><span>*</span>Name</label>
                                    </div>
                                    <div class="col-8">
                                        @auth
                                            <input type="text" id="name" class="custom-text-field" placeholder="{{ Auth::user()->first_name }}" readonly>
                                        @else
                                            <input type="text" id="name" name="name" class="custom-text-field" pattern="\S.*" title="Name cannot be all spaces" placeholder="Your name.." required>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            <div class="field-group mt-20">
                                <div class="row">
                                    <div class="col-4">
                                        <label for="email"><span>*</span>Email</label>
                                    </div>
                                    <div class="col-8">
                                        @auth
                                            <input type="email" id="email" class="custom-email-field" placeholder="{{ Auth::user()->email }}" readonly>
                                        @else
                                            <input type="email" id="email" class="custom-email-field" name="email" placeholder="Your email address.."  required>
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            <div class="field-group mt-20">
                                <div class="row">
                                    <div class="col-4 pe-0">
                                        <label for="mobile_no">Contact Number</label>
                                    </div>
                                    <div class="col-8">
                                        @auth
                                            <input type="text" id="mobile_no" class="custom-text-field" placeholder="{{ Auth::user()->mobile_no }}" readonly>
                                        @else
                                            <input type="number" id="mobile_no" name="mobile_no" class="custom-text-field" placeholder="Your mobile number.." pattern="[0-9]*" title="Only numeric values.">
                                        @endauth
                                    </div>
                                </div>
                            </div>
                            <p class="mt-10 text-end">- We will use this information for donation updates.</p>
                        </div>

                        <div class="submit-btn text-center">
                            <button type="submit" class="btn btn-sm subscribe-btn">Support</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
