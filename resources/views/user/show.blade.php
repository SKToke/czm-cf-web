@php
    use App\Enums\GenderTypeEnum;
    use App\Enums\ProfessionTypeEnum;
    use App\Enums\UserTypeEnum;
    use App\Enums\DistrictTypeEnum;
    use App\Enums\CountryTypeEnum;
@endphp
<x-main>
    <div class="container user-show py-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-body text-center pt-4">
                        <div class="profile-img">
                            @if(!isset($user->gender) || GenderTypeEnum::from($user->gender->value)->getTitle() === 'Other')
                                <img src="{{ asset('images/person.png') }}" alt="Profile Image">
                            @elseif(GenderTypeEnum::from($user->gender->value)->getTitle() === 'Female')
                                <img src="{{ asset('images/profile_female.png') }}" alt="Profile Image">
                            @elseif(GenderTypeEnum::from($user->gender->value)->getTitle() === 'Male')
                                <img src="{{ asset('images/profile_male.png') }}" alt="Profile Image">
                            @endif
                        </div>
                        <h5 class="my-3">{{ $user->first_name }}</h5>
                        <p class="text-muted mb-1">{{ $user->email }}</p>
                        @if ($user->role)
                        <p class="text-muted mb-1">{{ $user->role->title }}</p>
                        @endif
                        <p class="mb-4" style="color:{{ $user->active ? 'green' : 'red' }}">{{ $user->active ? 'Active' : 'Inactive' }}</p>
                        <div class="d-flex justify-content-center mb-2">
                            <a href="{{ route('user.edit', ['id' => auth()->id()]) }}" class="btn btn-outline-primary ms-1">Edit Profile</a>
                            <a href="{{ route('user.delete-account') }}" class="btn btn-outline-primary ms-1">Delete Account</a>
                            @if ($user->donor && $user->donor->active && $user->donor->donations->isNotEmpty())
                            <a href="{{ route('user_past_donation', $user) }}" class="btn btn-outline-primary ms-1">Donation History</a>
                            @endif
                            @if ($user->donor && $user->donor->active && $user->donor->donation_subscriptions->isNotEmpty())
                            <a href="{{ route('user_subscription_history', $user) }}" class="btn btn-outline-primary ms-1">Subscription History</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->first_name }}</p>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Email</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->email }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Mobile Number</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ str_replace('_', '', $user->mobile_no) }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">WhatsApp Number</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ str_replace('_', '', $user->whatsapp_no) }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Date Of Birth</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->date_of_birth }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Gender</p>
                            </div>
                            <div class="col-sm-9">
                                @if($user->gender)
                                    <p class="text-muted mb-0">{{ GenderTypeEnum::from($user->gender->value)->getTitle() }}</p>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Profession</p>
                            </div>
                            <div class="col-sm-9">
                                @if($user->profession)
                                    <p class="text-muted mb-0">{{ ProfessionTypeEnum::from($user->profession->value)->getTitle() }}</p>
                                @endif
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Account Type</p>
                            </div>
                            <div class="col-sm-9">
                                @if($user->user_type)
                                    <p class="text-muted mb-0">{{UserTypeEnum::from($user->user_type->value)->getTitle() }}</p>
                                @endif
                            </div>
                        </div>
                        <hr>


                        @if(!isset($user->user_type) || UserTypeEnum::from($user->user_type->value)->getTitle() === 'Business')
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Contact Person Name</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->contact_person_name }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Contact Person Mobile</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->contact_person_mobile }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Contact Person Designation</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->contact_person_designation }}</p>
                            </div>
                        </div>
                        <hr>
                        @endif

                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Address Line 1</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->address_line_1 }}</p>
                            </div>
                        </div>
                        <hr>

                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Address Line 2</p>
                            </div>
                            <div class="col-sm-9">
                                <p class="text-muted mb-0">{{ $user->address_line_2 }}</p>
                            </div>
                        </div>
                        <hr>
                        @if(!isset($user->country) || CountryTypeEnum::from($user->country->value)->getTitle() === 'Bangladesh')
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">Thana</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0">{{ $user->thana }}</p>
                                </div>
                            </div>
                            <hr>

                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0">District</p>
                                </div>
                                <div class="col-sm-9">
                                    @if($user->district)
                                        <p class="text-muted mb-0">{{ DistrictTypeEnum::from($user->district->value)->getTitle()  }}</p>
                                    @endif
                                </div>
                            </div>
                            <hr>
                        @endif

                        <div class="row">
                            <div class="col-sm-3">
                                <p class="mb-0">Country</p>
                            </div>
                            <div class="col-sm-9">
                                @if($user->country)
                                    <p class="text-muted mb-0">{{ CountryTypeEnum::from($user->country->value)->getTitle()  }}</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main>
