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
                    <h2 class="title mb-4">Personal Information</h2>
                    <span class="edit-icon"></span>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('user.update', $user) }}" class="personal-information-form">
                        @csrf

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="first_name">Name<span class="red-asterisk">*</span></label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" autocomplete="email" readonly value="{{ $user->email }}">
                            </div>

                            <div class="form-group">
                                <label for="mobile_no">Mobile no</label>
                                <input type="text" name="mobile_no" id="mobile_no" class="form-control" pattern="[0-9]*" title="only numeric values." value="{{ old('mobile_no', $user->mobile_no) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="whatsapp_no">WhatsApp Number</label>
                                <input type="text" name="whatsapp_no" id="whatsapp_no" class="form-control" pattern="[0-9]*" title="only numeric values." value="{{ old('whatsapp_no', $user->whatsapp_no) }}">
                            </div>

                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" max="{{ now()->toDateString() }}" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="gender">Gender</label>
                                <select name="gender" id="gender" class="form-control">
                                    <option value="">Select Gender</option>
                                    @foreach($genders as $value => $name)
                                        <option value="{{ $value }}" @if((string) old('gender', $user->gender?->value) === (string) $value) selected @endif>{{ ucwords(strtolower($name)) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="profession">Profession</label>
                                <select name="profession" id="profession" class="form-control">
                                    <option value="">Select Profession</option>
                                    @foreach($professions as $value => $name)
                                        <option value="{{ $value }}" {{ (string) old('profession', $user->profession?->value) === (string) $value ? 'selected' : '' }}>{{ $name }}</option>

                                    @endforeach
                                </select>

                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="user_type">Account Type</label>
                                <select name="user_type" id="user_user_type" class="form-control form-select">
                                    <option value="">Select Account Type</option>
                                    @foreach($userTypes as $value => $name)
                                        <option value="{{ $value }}" {{ (string) old('user_type', $user->user_type?->value) === (string) $value ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div id="contact-fields">
                            <div class="form-row">
                                <div class="form-group-bottom">
                                    <label for="user_contact_person_name">Contact Person's Name<span class="red-asterisk">*</span></label>
                                    <input type="text" name="contact_person_name" id="user_contact_person_name" class="form-control" value="{{ old('contact_person_name', $user->contact_person_name) }}">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group-bottom">
                                    <label for="user_contact_person_mobile">Contact Person's Mobile Number<span class="red-asterisk">*</span></label>
                                    <input type="text" name="contact_person_mobile" id="user_contact_person_mobile" class="form-control" pattern="[0-9]*" title="only numeric values." value="{{ old('contact_person_mobile', $user->contact_person_mobile) }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group-bottom">
                                    <label for="user_contact_person_designation">Contact Person Designation<span class="red-asterisk">*</span></label>
                                    <input type="text" name="contact_person_designation" id="user_contact_person_designation" class="form-control" value="{{ old('contact_person_designation', $user->contact_person_designation) }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="address_line_1">Address Line 1</label>
                                <input type="text" name="address_line_1" id="address_line_1" class="form-control" placeholder="Flat, Floor, House no, House Name" value="{{ old('address_line_1', $user->address_line_1) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="address_line_2">Address Line 2 (optional)</label>
                                <input type="text" name="address_line_2" id="address_line_2" class="form-control" placeholder="Road, Block, Area, Near Landmark" value="{{ old('address_line_2', $user->address_line_2) }}">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="country">Country</label>
                                <select name="country" id="country" class="form-control">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $value => $name)
                                        <option value="{{ $value }}" {{ (string) old('country', $user->country?->value) == (string) $value ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="bangladeshi-address">
                            <div class="form-row">
                                <div class="form-group-bottom">
                                    <label for="district">District</label>
                                    <select name="district" id="district" class="form-control">
                                        <option value="">Select District</option>
                                        @foreach($districts as $value => $name)
                                            <option value="{{ $value }}" {{ (string) old('district', $user->district?->value) == (string) $value ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group-bottom">
                                    <label for="thana">Thana</label>
                                    <input type="text" name="thana" id="thana" class="form-control" value="{{ old('thana', $user->thana) }}">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group-bottom">
                                    <label for="post_code">Post Code</label>
                                    <input type="text" name="post_code" id="post_code" class="form-control" value="{{ old('post_code', $user->post_code ?? '') }}">
                                </div>
                            </div>


                        </div>

                        <div id="other-address">

                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="password">Password - leave blank if you don't want to change it</label>
                                <input type="password" name="password" id="password" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" autocomplete="new-password">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group-bottom">
                                <label for="current_password">Current Password (Leave blank if you don't want to change the password)</label>
                                <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                            </div>
                        </div>

                        <div class="form-row d-flex justify-content-end w-100">
                            <button type="submit" class="btn btn-primary save-button">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-main>
<script src="{{ asset('js/edit_user_profile.js') }}"></script>
