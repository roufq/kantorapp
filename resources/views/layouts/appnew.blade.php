<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $authUser = auth()->user();
        $brandLocation = $authUser && method_exists($authUser, 'location') ? $authUser->location : null;
        $brandName = $brandLocation && $brandLocation->brand_name ? $brandLocation->brand_name : 'KantorApp';
        $primaryColor = '#10b981'; // Mint green from image
    @endphp
    <title>{{ $brandName }} | Digital Workplace</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}?v={{ time() }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v={{ time() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">

    <link rel="stylesheet" href="{{ asset('kantorapp/css/app-modern.css') }}?v={{ time() }}">
    @stack('styles')
</head>

<body>
    <div id="wrapper">
        @auth
            <!-- Sidebar -->
            <div class="side-menu">
                <div class="logo-box d-flex align-items-center">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo-img"
                        style="width: 40px; height: 40px; margin-right: 12px; border-radius: 10px;">
                    <span class="brand-label">KantorApp</span>
                </div>

                <ul class="menu-list">
                    <li class="menu-title"
                        style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                        DASHBOARD & CHATS</li>
                    <li>
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="mdi mdi-home-outline"></i> <span>{{ __('Dashboard') }}</span>
                        </a>
                    </li>
                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                        <li>
                            <a href="{{ route('messages.index') }}"
                                class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
                                <i class="mdi mdi-email-outline"></i> <span>{{ __('Messages') }}</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="{{ route('tasks.index') }}" class="{{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                            <i class="mdi mdi-checkbox-multiple-marked-outline"></i> <span>{{ __('Tasks') }}</span>
                        </a>
                    </li>

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                        @php
                            $pendingCreationCount = \App\Models\Task::where('requires_approval', true)
                                ->where('approval_status', 'pending')
                                ->when($authUser->hasRole('Location Admin'), function ($q) use ($authUser) {
                                    $q->where('approval_level', 'location_admin')
                                        ->whereHas('assignee', function ($qq) use ($authUser) {
                                            $qq->where('location_id', $authUser->location_id);
                                        });
                                })->count();
                        @endphp
                        <li>
                            <a href="{{ route('tasks.progress.approvals') }}"
                                class="{{ request()->routeIs('tasks.progress.approvals') ? 'active' : '' }}">
                                <i class="mdi mdi-shield-check-outline"></i>
                                <span>{{ __('Task Approvals') }} @if($pendingCreationCount > 0)<span
                                class="badge rounded-pill bg-warning text-dark ms-2">{{ $pendingCreationCount }}</span>@endif</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('shifts.rosters.calendar') }}"
                                class="{{ request()->routeIs('shifts.rosters.calendar') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-blank-multiple"></i> <span>{{ __('Calendar') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                        <li>
                            <a href="{{ route('employees.index') }}"
                                class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-group-outline"></i> <span>{{ __('Employees') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                        <li>
                            <a href="{{ route('location-change-requests.index') }}"
                                class="{{ request()->routeIs('location-change-requests.*') ? 'active' : '' }}">
                                <i class="mdi mdi-map-marker-distance"></i> <span>{{ __('Location Change') }}</span>
                            </a>
                        </li>
                    @endif

                    <li class="menu-title"
                        style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                        TIME & ATTENDANCE</li>
                    <li>
                        <a href="{{ route('attendance.checkin') }}"
                            class="{{ request()->routeIs('attendance.checkin') ? 'active' : '' }}">
                            <i class="mdi mdi-clock-check-outline"></i> <span>{{ __('Check In/Out') }}</span>
                        </a>
                    </li>
                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                        <li>
                            <a href="{{ route('attendance.report') }}"
                                class="{{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-search"></i> <span>{{ __('Attendance Report') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('attendance.absences') }}"
                                class="{{ request()->routeIs('attendance.absences') ? 'active' : '' }}">
                                <i class="mdi mdi-account-alert-outline"></i> <span>{{ __('Absences') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('attendance.recap') }}"
                                class="{{ request()->routeIs('attendance.recap') ? 'active' : '' }}">
                                <i class="mdi mdi-clipboard-text-play-outline"></i> <span>{{ __('Recap') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                        <li>
                            <a href="{{ route('overtime.index') }}"
                                class="{{ request()->routeIs('overtime.*') ? 'active' : '' }}">
                                <i class="mdi mdi-progress-clock"></i> <span>{{ __('Overtime') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}"
                                class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="mdi mdi-clipboard-text-outline"></i> <span>{{ __('Reports') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                        <li>
                            <a href="{{ route('work-recaps.index') }}"
                                class="{{ request()->routeIs('work-recaps.*') ? 'active' : '' }}">
                                <i class="mdi mdi-timer-sand"></i> <span>{{ __('Working Hours Recap') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('work-targets.index') }}"
                                class="{{ request()->routeIs('work-targets.*') ? 'active' : '' }}">
                                <i class="mdi mdi-bullseye-arrow"></i> <span>{{ __('Target Working Hours') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                        @php
                            $today = now()->toDateString();
                            if ($authUser->hasRole('Super Admin')) {
                                $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date', '>=', $today)->count();
                            } else {
                                $locId = $authUser->location_id;
                                $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date', '>=', $today)
                                    ->where(function ($q) use ($locId) {
                                        $q->where('is_national', true)->orWhere(function ($qq) use ($locId) {
                                            $qq->where('is_national', false)->where('location_id', $locId); });
                                    })->count();
                            }
                        @endphp
                        <li>
                            <a href="{{ route('holidays.index') }}"
                                class="{{ request()->routeIs('holidays.*') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-star-outline"></i>
                                <span>{{ __('Holidays') }} @if($upcomingHolidayCount > 0)<span
                                class="badge rounded-pill bg-info text-dark ms-2">{{ $upcomingHolidayCount }}</span>@endif</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('weekly-offs.index') }}"
                                class="{{ request()->routeIs('weekly-offs.*') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-x"></i> <span>{{ __('Weekly Offs') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                        @php
                            if ($authUser->hasRole('Super Admin')) {
                                $pendingLeavesCount = \App\Models\EmployeeLeave::where('status', 'pending')->count();
                            } elseif ($authUser->hasRole('Location Admin')) {
                                $pendingLeavesCount = \App\Models\EmployeeLeave::where('status', 'pending')->where('location_id', $authUser->location_id)->count();
                            } else {
                                $pendingLeavesCount = 0;
                            }
                        @endphp
                        <li>
                            <a href="{{ route('leaves.index') }}" class="{{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-arrow-right-outline"></i>
                                <span>{{ __('Leaves') }}
                                    @if($pendingLeavesCount > 0 && ($authUser->hasRole('Super Admin') || $authUser->hasRole('Location Admin')))<span
                                    class="badge rounded-pill bg-warning text-dark ms-2">{{ $pendingLeavesCount }}</span>@endif</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasRole('Location Admin'))
                        <li class="menu-title"
                            style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                            LOCATION ADMIN</li>
                        <li>
                            <a href="{{ route('location-admin-tasks.index') }}"
                                class="{{ request()->routeIs('location-admin-tasks.*') ? 'active' : '' }}">
                                <i class="mdi mdi-map-marker-radius"></i> <span>{{ __('Location Tasks') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('shifts.rosters.index') }}"
                                class="{{ request()->routeIs('shifts.rosters.*') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-clock-outline"></i> <span>{{ __('Weekly Rosters') }}</span>
                            </a>
                        </li>
                        @if($authUser->location_id)
                            <li>
                                <a href="{{ route('locations.settings', $authUser->location_id) }}"
                                    class="{{ request()->routeIs('locations.settings*') ? 'active' : '' }}">
                                    <i class="mdi mdi-office-building-cog-outline"></i>
                                    <span>{{ __('My Location Settings') }}</span>
                                </a>
                            </li>
                        @endif
                    @endif

                    @if($authUser->hasAnyRole(['Super Admin', 'HR']))
                        <li class="menu-title"
                            style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                            TALENT & HR</li>
                        <li>
                            <a href="{{ route('jobdesks.index') }}"
                                class="{{ request()->routeIs('jobdesks.*') ? 'active' : '' }}">
                                <i class="mdi mdi-briefcase-variant-outline"></i> <span>{{ __('Jobdesk & Catalog') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('jobdesk-targets.index') }}"
                                class="{{ request()->routeIs('jobdesk-targets.*') ? 'active' : '' }}">
                                <i class="mdi mdi-bullseye-arrow"></i> <span>{{ __('Target Output') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('approval-rules.index') }}"
                                class="{{ request()->routeIs('approval-rules.*') ? 'active' : '' }}">
                                <i class="mdi mdi-playlist-check"></i> <span>{{ __('Approval Rules') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employee-positions.index') }}"
                                class="{{ request()->routeIs('employee-positions.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-tie-outline"></i> <span>{{ __('Position History') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employee-transfers.index') }}"
                                class="{{ request()->routeIs('employee-transfers.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-switch-outline"></i> <span>{{ __('Transfers') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('employee-contracts.index') }}"
                                class="{{ request()->routeIs('employee-contracts.*') ? 'active' : '' }}">
                                <i class="mdi mdi-file-certificate-outline"></i> <span>{{ __('Contracts') }}</span>
                            </a>
                        </li>
                    @endif

                    @if($authUser->hasRole('Super Admin'))
                        <li class="menu-title"
                            style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                            ADMINISTRATION</li>
                        <li>
                            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="mdi mdi-badge-account-horizontal-outline"></i> <span>{{ __('Users') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('divisions.index') }}"
                                class="{{ request()->routeIs('divisions.*') ? 'active' : '' }}">
                                <i class="mdi mdi-layers-triple-outline"></i> <span>{{ __('Divisions') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('locations.index') }}"
                                class="{{ request()->routeIs('locations.index') || request()->routeIs('locations.show') || request()->routeIs('locations.edit') ? 'active' : '' }}">
                                <i class="mdi mdi-map-search-outline"></i> <span>{{ __('All Locations') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('location-shifts.index') }}"
                                class="{{ request()->routeIs('location-shifts.*') ? 'active' : '' }}">
                                <i class="mdi mdi-clock-fast"></i> <span>{{ __('Location Shifts') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('location-admins.index') }}"
                                class="{{ request()->routeIs('location-admins.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-cog-outline"></i> <span>{{ __('Location Admins') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endauth

        <!-- Main Content Wrapper -->
        <div class="main-content">
            @auth
                <div class="mobile-header">
                    <div class="d-flex align-items-center">
                        <button class="btn p-0 me-3 text-dark sidebar-toggle" id="mobile-toggle">
                            <i class="mdi mdi-menu fs-1"></i>
                        </button>
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo"
                            style="width: 32px; height: 32px; margin-right: 10px; border-radius: 8px;">
                        <span class="fw-bold fs-4">KantorApp</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="mdi mdi-bell-outline fs-4 text-muted"></i>
                        <a href="{{ route('profile.show') }}">
                            <img src="{{ $authUser->profile_photo_path ? asset('storage/' . $authUser->profile_photo_path) : asset('assets/img/user2-160x160.jpg') }}"
                                class="rounded-pill" style="width: 32px; height: 32px;" alt="">
                        </a>
                    </div>
                </div>

                <!-- Top Navigation -->
                <div class="top-nav">
                    <div class="greeting">
                        <h1>Good Morning, {{ explode(' ', $authUser->name)[0] }}!</h1>
                        <p>{{ now()->format('D, M d, Y, h:i A') }}</p>
                    </div>

                    <div class="top-actions">
                        <div class="search-pill">
                            <i class="mdi mdi-magnify"></i>
                            <input type="text" placeholder="Search">
                        </div>

                        @php
                            $unreadNotificationCount = $authUser->unreadNotifications()->count();
                            $taskNotifTypes = [
                                \App\Notifications\TaskSlotApprovalNotification::class,
                                \App\Notifications\TaskApprovalNotification::class,
                            ];
                            $recentTaskNotifs = $authUser->unreadNotifications()
                                ->whereIn('type', $taskNotifTypes)
                                ->latest()
                                ->take(5)
                                ->get();
                        @endphp
                        <div class="dropdown">
                            <div class="nav-icon-btn dropdown-toggle hide-caret" id="notifDropdown" data-toggle="dropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-bell-outline"></i>
                                @if($unreadNotificationCount > 0)
                                    <div class="badge-dot"></div>
                                @endif
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notifDropdown"
                                style="border-radius: 20px; min-width: 320px; padding: 15px; z-index: 1060; margin-top: 20px !important;">
                                <li class="px-3 py-2 border-bottom mb-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ __('Notifications') }}</h6>
                                    <span class="badge bg-soft-success text-success">{{ $unreadNotificationCount }}
                                        New</span>
                                </li>
                                @if($recentTaskNotifs->count() > 0)
                                    @foreach($recentTaskNotifs as $notif)
                                        @php
                                            $data = $notif->data ?? [];
                                            $title = $data['task_title'] ?? 'Task Update';
                                            $status = $data['status'] ?? 'info';
                                        @endphp
                                        <li>
                                            <a class="dropdown-item py-2 px-3 rounded-4 mb-1" href="{{ route('tasks.index') }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="activity-icon bg-soft-info text-info me-3"
                                                        style="width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;">
                                                        <i class="mdi mdi-calendar-check fs-6"></i>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <p class="mb-0 fw-bold text-truncate" style="font-size: 0.85rem;">
                                                            {{ $title }}</p>
                                                        <small class="text-muted"
                                                            style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    @endforeach
                                @else
                                    <li class="text-center py-4 text-muted">
                                        <i class="mdi mdi-bell-off-outline fs-1 opacity-25"></i>
                                        <p class="mt-2 small fw-medium">No new notifications</p>
                                    </li>
                                @endif
                                <li>
                                    <hr class="dropdown-divider mx-2 opacity-10">
                                </li>
                                <li>
                                    <a class="dropdown-item text-center fw-bold py-2 text-primary"
                                        href="{{ route('tasks.index') }}">
                                        {{ __('View All Notifications') }}
                                    </a>
                                </li>
                            </ul>
                        </div>

                        @php
                            $avatarUrl = $authUser->profile_photo_path ? asset('storage/' . $authUser->profile_photo_path) : asset('assets/img/user2-160x160.jpg');
                        @endphp
                        <div class="dropdown">
                            <button class="btn p-0 border-0 dropdown-toggle hide-caret" type="button" id="userDropdown"
                                data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="{{ $avatarUrl }}" class="user-profile-img" alt="Profile" style="cursor: pointer;">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown"
                                style="border-radius: 20px; min-width: 260px; padding: 15px; z-index: 1060; margin-top: 20px !important;">
                                <li class="px-3 py-3 border-bottom mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-0 fw-bold text-truncate"
                                                style="font-size: 1rem; max-width: 150px;">{{ $authUser->name }}</h6>
                                            <small class="text-muted fw-semibold"
                                                style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $authUser->getRoleNames()->first() }}</small>
                                        </div>
                                        <div class="badge bg-soft-success text-success rounded-pill px-2 py-1"
                                            style="font-size: 0.7rem;">Active</div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 px-3 rounded-pill fw-medium mb-1 mt-1"
                                        href="{{ route('profile.show') }}">
                                        <i class="mdi mdi-account-circle-outline me-2 fs-5 opacity-75"></i>
                                        {{ __('My Profile') }}
                                    </a>
                                </li>
                                @php $twoFactorEnabled = $authUser->hasTwoFactorEnabled(); @endphp
                                <li>
                                    <a class="dropdown-item py-2 px-3 rounded-pill fw-medium mb-1"
                                        href="{{ route('2fa.setup') }}">
                                        <i class="mdi mdi-shield-link-variant-outline me-2 fs-5 opacity-75"></i>
                                        {{ $twoFactorEnabled ? __('Security Settings') : __('Enable 2FA') }}
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider mx-2 opacity-10">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="dropdown-item py-2 px-3 rounded-pill fw-bold text-danger">
                                            <i class="mdi mdi-power-cycle me-2 fs-5 opacity-75"></i> {{ __('Sign Out') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Page Content -->
            <div class="page-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('kantorapp/js/app-modern.js') }}?v={{ time() }}"></script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>