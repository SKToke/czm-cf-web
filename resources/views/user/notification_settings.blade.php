@php
    use App\Enums\NotificationFrequencyTypeEnum;
@endphp
<x-main>
    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text">Notification Settings</h3>
    <p class="d-flex justify-content-center">Add you personal preferences to manage notifications on your profile</p>
    <div class="container notifications-settings mt-30 w-50">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mt-10 mb-30">
                    <div class="card-body ps-5 pe-5">
                        <form method="POST" action="{{ route('save-notification-settings') }}">
                            @csrf
                            @php
                                if ($userSettings instanceof Illuminate\Support\Collection) {
                                    $userSettings = $userSettings->first();
                                }
                            @endphp
                            <div class="form-check form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_general_type))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" name="allow_general_type" id="allow_general_type" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_general_type" name="allow_general_type" value="1">
                                @endif
                                <label for="allow_general_type" class="form-check-label label-col"><span>Allow General Type Notifications</span></label>
                            </div>

                            <div class="form-check form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_campaign_launch))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" id="allow_campaign_launch" name="allow_campaign_launch" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_campaign_launch" name="allow_campaign_launch" value="1">
                                @endif
                                <label for="allow_campaign_launch" class="form-check-label label-col"><span>Allow Campaign Launch Notifications</span></label>
                            </div>

                            <div class="form-check form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_campaign_milestone))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" id="allow_campaign_milestone" name="allow_campaign_milestone" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_campaign_milestone" name="allow_campaign_milestone" value="1">
                                @endif
                                <label for="allow_campaign_milestone" class="form-check-label label-col"><span>Allow Campaign Milestone Notifications</span></label>
                            </div>

                            <div class="form-group form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_campaign_countdown))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" id="allow_campaign_countdown" name="allow_campaign_countdown" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_campaign_countdown" name="allow_campaign_countdown" value="1">
                                @endif
                                <label for="allow_campaign_countdown" class="form-check-label label-col"><span>Allow Campaign Countdown Notifications</span></label>
                            </div>

                            <div class="form-group form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_campaign_progress))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" id="allow_campaign_progress" name="allow_campaign_progress" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_campaign_progress" name="allow_campaign_progress" value="1">
                                @endif
                                <label for="allow_campaign_progress" class="form-check-label label-col">Allow Campaign Progress Update Notifications</label>
                            </div>

                            <div class="form-group form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_campaign_reminder))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" id="allow_campaign_reminder" name="allow_campaign_reminder" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_campaign_reminder" name="allow_campaign_reminder" value="1">
                                @endif
                                <label for="allow_campaign_reminder" class="form-check-label label-col">Allow Campaign Reminder Notifications</label>
                            </div>

                            <div class="form-group form-switch settings-form">
                                @if (is_null($userSettings) || ($userSettings->count() > 0 && $userSettings->allow_gratitude))
                                    <input checked="checked" type="checkbox" class="form-check-input input-col" id="allow_gratitude" name="allow_gratitude" value="1">
                                @else
                                    <input type="checkbox" class="form-check-input input-col" id="allow_gratitude" name="allow_gratitude" value="1">
                                @endif
                                <label for="allow_gratitude" class="form-check-label label-col">Allow Expression of Gratitude Notifications</label>
                            </div>

                            <div class="form-group row">
                                <hr class="mt-4 mb-4">
                                <label for="frequency" class="col-md-8 col-form-label text-md-right">Notification Frequency</label>

                                <div class="col-md-4 input-col">
                                    <select id="frequency" name="frequency" class="form-control">
                                        @if (!is_null($userSettings) && $userSettings->count() > 0)
                                            @foreach(NotificationFrequencyTypeEnum::toArray() as $key => $value)
                                                <option value="{{ $key }}" {{ $userSettings->frequency?->value == $key ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        @else
                                            @foreach(NotificationFrequencyTypeEnum::toArray() as $key => $value)
                                                <option value="{{ $key }}" {{ $loop->first ? 'selected' : '' }}>{{ $value }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mt-30">
                                <div class="text-center">
                                    <button type="submit" class="btn btn-primary submit-btn">Save Your Changes</button>
                                    <a href="{{ route('user-notifications') }}" class="btn submit-btn btn-primary">Back</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-main>
