@php
    use OpenAdmin\Admin\Facades\Admin;
@endphp
<div class="czm-navigation">
    <div class="nav-header">
        <ul>
            <li class="dropdown">
                @php
                    $notifications = 0;
                    $currentUser = auth()->user();

                    if ($currentUser && $currentUser->active && $currentUser->getUnreadNotifications()) {
                        $notifications = $currentUser->getUnreadNotifications()->count();
                    }
                @endphp
                <div class="notification-container"
                @php
                 if ($notifications >= 0) : echo 'data-user="'. $currentUser?->id .'" data-notification-count="'.$notifications.'"';endif;
               @endphp
               >
                    <p class="mt-4 czm-auth-container {{ auth()->guest() ? '' : 'active' }}"></p>

                    <?php if ($notifications <= 0): ?>
                    <div class="notification-badge" id="red-badge-for-notification" style="display: none;">{{$notifications}}</div>
                    <?php else: ?>
                    <div class="notification-badge" id="red-badge-for-notification">{{$notifications}}</div>
                    <?php endif; ?>
                </div>
                <ul class="submenu shadow">
                    @if(auth()->guest())
                        <li>
                            <a data-bs-toggle="modal"  id="require-register">Register</a>
                        </li>
                        <li>
                            <a data-bs-toggle="modal" id="require-login">Login</a>
                        </li>
                    @else
                        <li>
                            <div class="czm-username fw-bold text-center" href="#">{{ auth()->user()->email }}</div>
                        </li>
                        <li>
                            <a href="{{ route('user.show', ['id' => auth()->id()]) }}">Dashboard</a>
                        </li>
                        @if (auth()->user()->hasValidRoles())
                        <li>
                            <a href="{{ route('user.admin-dashboard', ['id' => auth()->id()]) }}">Admin Dashboard</a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('user-donations') }}">Donations</a>
                        </li>
                        <li>
                            <a href="{{ route('user.campaign-supscription-history', ['id' => auth()->user()->id]) }}">Subscription History</a>
                        </li>
                        <li>
                            <a href="{{ route('archived-zakat-calculations') }}">Archived Zakat Calculations</a>
                        </li>
                        <li>
                            <a href="{{ route('user-payments') }}">Your Payments</a>
                        </li>
                        <li>
                            <a href="{{ route('user-notifications') }}">Notifications</a>
                        </li>
                        <li>
                            <a href="#" id="logout-link">Logout</a>
                            <form class="d-none" id="logout-form"
                                  action="{{ route('logout') }}" method="POST">
                                @csrf
                            </form>
                        </li>
                    @endif
                </ul>
            </li>
        </ul>
    </div>
</div>

<!-- Register Modal -->
<div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @include('auth.register')
            </div>
        </div>
    </div>
</div>

<!-- Login Modal -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">
                @include('auth.login')
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/auth.js') }}"></script>
