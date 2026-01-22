@php
use App\Enums\GenderTypeEnum;
@endphp
<x-main>
    <div class="container profile w-100 p-4">
        <div class="row">
            <div class="col-md-4 d-flex justify-content-center align-content-lg-start">
                <div class="profile-card">
                    <div class="profile-picture">
                        @if(!isset($user->gender) || GenderTypeEnum::from($user->gender->value)->getTitle() === 'Other')
                        <img src="{{ asset('images/person.png') }}" alt="Profile Image">
                        @elseif(GenderTypeEnum::from($user->gender->value)->getTitle() === 'Female')
                        <img src="{{ asset('images/profile_female.png') }}" alt="Profile Image">
                        @elseif(GenderTypeEnum::from($user->gender->value)->getTitle() === 'Male')
                        <img src="{{ asset('images/profile_male.png') }}" alt="Profile Image">
                        @endif
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name">{{ $user->first_name }}</h1>
                        <p class="profile-email">{{ $user->email }}</p>
                        @if(isset($user->role))
                        <p class="profile-role">{{ $user->role->title }}</p>
                        @endif
                        <p class="mb-4" style="color:{{ $user->active ? 'green' : 'red' }}">{{ $user->active ? 'Active' : 'Inactive' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="personal-information-container">
                    <h2 class="title mb-4">Deactivate Account</h2>

                    <form method="POST" action="{{ route('user.deactivate', $user) }}" class="personal-information-form">
                        @csrf


                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" autocomplete="email" readonly value="{{ $user->email }}">
                            </div>

                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="password">Password </label>
                                <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-row d-flex justify-content-end w-100">
                            <button type="submit" class="btn btn-primary save-button">Deactivate</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-main>
<script src="{{ asset('js/edit_user_profile.js') }}"></script>
