<x-main>
    <h3 class="mt-4 d-flex justify-content-center fw-bold czm-primary-text mb-30">Notification List</h3>
    <div class="container notifications w-50">
        <div class="row mb-3">
            <div class="col">
                <a href="{{ route('notification-settings') }}" class="btn btn-sm btn-primary settings-btn float-end"><i class="fa-solid fa-gear me-2"></i>Settings</a>
            </div>
        </div>
        <ul class="nav nav-tabs mb-3" id="notificationTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="newNotificationsTab" data-bs-toggle="tab" href="#newNotifications" role="tab" aria-controls="newNotifications" aria-selected="true">New</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="archivedNotificationsTab" data-bs-toggle="tab" href="#archivedNotifications" role="tab" aria-controls="archivedNotifications" aria-selected="false">Viewed</a>
            </li>
        </ul>
        <div class="tab-content" id="notificationTabsContent">
            <div class="tab-pane fade show active" id="newNotifications" role="tabpanel" aria-labelledby="newNotificationsTab">
                @if($notifications && $notifications->count() > 0)
                    @foreach($notifications as $notification)
                        <div class="notification-body justify-content-center">
                            <ul class="rounded-2 border bg-primary-subtle pt-3 pe-3 pb-3">
                                <li class="text-dark">
                                    <strong>{{ $notification->notification_title }}</strong>
                                    <p>{!! $notification->notification_description !!}</p>
                                    @if ($notification->campaign_id)
                                        <p>Visit campaign Page:
                                        <a href="{{ route('campaign-details', ['slug' => $notification->campaign->slug] )}}">
                                            <strong>{{ $notification->campaign->title }}</strong>
                                        </a>
                                        </p>
                                    @endif
                                    <div class="row">
                                        <div class="col">
                                            <form action="{{ route('notifications.mark-as-read', ['id' => $notification->id]) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success mark-btn">Mark as Read</button>
                                            </form>
                                        </div>
                                        <div class="col">
                                            <p class="text-end">{{ $notification->created_at }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    @endforeach
                @else
                    <div class="text-center mb-4">
                        <p>No New Notifications.</p>
                    </div>
                @endif
            </div>
            <div class="tab-pane fade" id="archivedNotifications" role="tabpanel" aria-labelledby="archivedNotificationsTab">
                @foreach($archivedNotifications as $notification)
                    <div class="notification-body justify-content-center">
                        <ul class="rounded-2 border bg-primary-subtle pt-3 pe-3 pb-3">
                            <li class="text-dark">
                                <strong>{{ $notification->notification_title }}</strong>
                                <p>{!! $notification->notification_description !!}</p>
                                @if ($notification->campaign_id)
                                    <p>Visit campaign Page:
                                        <a href="{{ route('campaign-details', ['slug' => $notification->campaign->slug] )}}">
                                            <strong>{{ $notification->campaign->title }}</strong>
                                        </a>
                                    </p>
                                @endif
                                <div class="row">
                                    <div class="col">
                                        <p class="text-start">- {{ $notification->created_at }}</p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-main>
