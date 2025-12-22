<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        $authUser = auth()->user();
        $brandLocation = $authUser && method_exists($authUser, 'location') ? $authUser->location : null;
        $brandName = $brandLocation && $brandLocation->brand_name ? $brandLocation->brand_name : 'Office App';
        $brandLogoUrl = $brandLocation && $brandLocation->brand_logo_url
            ? (\Illuminate\Support\Str::startsWith($brandLocation->brand_logo_url, ['http://', 'https://'])
                ? $brandLocation->brand_logo_url
                : asset($brandLocation->brand_logo_url))
            : null;
        $primaryColor = $brandLocation && $brandLocation->primary_color ? $brandLocation->primary_color : null;
        $secondaryColor = $brandLocation && $brandLocation->secondary_color ? $brandLocation->secondary_color : null;
    @endphp
    <title>{{ $brandName }} | Portal</title>
    <meta content="Admin Dashboard" name="description">
    <meta content="Mannatthemes" name="author">

    <link rel="shortcut icon" href="{{ asset('NewAsset/assets/images/favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('NewAsset/assets/plugins/morris/morris.css') }}">
    <link rel="stylesheet" href="{{ asset('NewAsset/assets/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('NewAsset/assets/css/icons.css') }}" type="text/css">
    <link rel="stylesheet" href="{{ asset('NewAsset/assets/css/style.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" crossorigin="anonymous">
    <!-- Extra icon CDN to ensure all mdi icons are available -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
    @if($primaryColor || $secondaryColor)
    <style>
        :root {
            @if($primaryColor) --brand-primary: {{ $primaryColor }}; @endif
            @if($secondaryColor) --brand-secondary: {{ $secondaryColor }}; @endif
        }
        .bg-primary { background-color: var(--brand-primary, #3c4ccf) !important; }
        .text-primary { color: var(--brand-primary, #3c4ccf) !important; }
        .btn-primary { background-color: var(--brand-primary, #3c4ccf); border-color: var(--brand-primary, #3c4ccf); }
        .badge-primary { background-color: var(--brand-primary, #3c4ccf); color: #fff; }
    </style>
    @endif
    <style>
        /* Basic compatibility helpers so existing Blade views keep working on the new skin */
        .form-select { display: block; width: 100%; padding: .375rem .75rem; font-size: 1rem; line-height: 1.5; color: #495057; background-color: #fff; border: 1px solid #ced4da; border-radius: .25rem; }
        .table .code-badge { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace; }
        .sidebar-inner ul li .badge { margin-left: .5rem; }
        /* Profile dropdown readability tweaks */
        .profile-dropdown { min-width: 220px; }
        .profile-dropdown .dropdown-item.notif-title { background: #f4f6f9; color: #1f2d3d; }
        .profile-dropdown .dropdown-item.notif-title h5 { margin-bottom: .2rem; font-weight: 700; color: #1f2d3d; }
        .profile-dropdown .dropdown-item.notif-title small { color: #5b6b7a; }
        .profile-dropdown .dropdown-item { color: #2c3e50; }
        .profile-dropdown .dropdown-item i { color: #5c6b7a; }
        .profile-dropdown .dropdown-item:hover,
        .profile-dropdown .dropdown-item:focus { background-color: #eef2f6; color: #1f2d3d; }
        .profile-dropdown .btn-primary { background-color: #5a67d8; border-color: #5a67d8; }
        .topbar .nav-user span { color: #fff; }
        .topbar .nav-user .mdi-chevron-down { color: #fff; }
        .content-page .page-content-wrapper { padding-top: 18px; }
        .content-page .page-content-wrapper > .container-fluid { margin-top: 8px; }
        .table .col-status { width: 120px; min-width: 120px; text-align: center; }
        .table .status-badge { display: inline-block; min-width: 90px; }
        .overtime-filter .form-control { height: 38px; }
        .overtime-filter select.form-control { height: 38px; line-height: 1.5; padding-top: 0.375rem; padding-bottom: 0.375rem; padding-right: 2rem; }
        .overtime-filter .form-label { display: block; line-height: 1.2; }
        .overtime-filter .btn { height: 38px; line-height: 1.2; }
        .overtime-filter .overtime-filter-inputs { align-items: center; }
        .overtime-filter .overtime-filter-tight { padding-left: 6px; }
        .overtime-filter .overtime-filter-search { padding-right: 4px; }
        .overtime-filter .overtime-filter-status { padding-left: 4px; }
        @media (min-width: 768px) {
            .overtime-filter .overtime-filter-status-select { margin-left: 0; }
            .overtime-filter .overtime-filter-status-shift { position: relative; left: -16px; }
        }
        .overtime-filter .overtime-filter-center { text-align: left; }
        .overtime-filter .overtime-filter-center select.form-control { width: 100%; margin: 0; }
        @media (min-width: 992px) {
            .overtime-filter .overtime-filter-inputs { flex-wrap: nowrap; }
        }
    </style>
    @stack('styles')
</head>

<body class="fixed-left">
    <!-- Loader -->
    <div id="preloader">
        <div id="status"><div class="spinner"></div></div>
    </div>

    <div id="wrapper">
        @auth
        <!-- ========== Left Sidebar Start ========== -->
        <div class="left side-menu">
            <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
                <i class="ion-close"></i>
            </button>
            <div class="topbar-left">
                <div class="text-center">
                    <a href="{{ route('dashboard') }}" class="logo">
                        @if($brandLogoUrl)
                            <img src="{{ $brandLogoUrl }}" alt="{{ $brandName }}" height="26">
                        @else
                            <i class="mdi mdi-assistant"></i> {{ $brandName }}
                        @endif
                    </a>
                </div>
            </div>
            <div class="sidebar-inner slimscrollleft">
                <div id="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li>
                            <a href="{{ route('dashboard') }}" class="waves-effect {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                                <i class="mdi mdi-airplay"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan'))
                        <li>
                            <a href="{{ route('messages.index') }}" class="waves-effect {{ request()->routeIs('messages.*') ? 'active' : '' }}">
                                <i class="mdi mdi-email"></i>
                                <span> Messages </span>
                            </a>
                        </li>
                        @endif
                        <li>
                            <a href="{{ route('tasks.index') }}" class="waves-effect {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                                <i class="mdi mdi-clipboard-text-outline"></i>
                                <span> Tasks </span>
                            </a>
                        </li>
                        @if($authUser->hasAnyRole(['Super Admin','Admin Lokasi']))
                        <li>
                            @php
                                $pendingCreationCount = \App\Models\Task::where('requires_approval', true)
                                    ->where('approval_status', 'pending')
                                    ->when($authUser->hasRole('Admin Lokasi'), function ($q) use ($authUser) {
                                        $q->where('approval_level', 'location_admin')
                                          ->whereHas('assignee', function ($qq) use ($authUser) {
                                              $qq->where('location_id', $authUser->location_id);
                                          });
                                    })
                                    ->count();
                            @endphp
                            <a href="{{ route('tasks.progress.approvals') }}" class="waves-effect {{ request()->routeIs('tasks.progress.approvals') ? 'active' : '' }}">
                                <i class="mdi mdi-check-decagram"></i>
                                <span> Approval Tugas @if($pendingCreationCount>0)<span class="badge badge-warning float-right">{{ $pendingCreationCount }}</span>@endif</span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasAnyRole(['Super Admin','Admin Lokasi','Karyawan']))
                        <li>
                            <a href="{{ route('shifts.rosters.calendar') }}" class="waves-effect {{ request()->routeIs('shifts.rosters.calendar') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-clock"></i>
                                <span> Kalender </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi'))
                        <li>
                            <a href="{{ route('karyawans.index') }}" class="waves-effect {{ request()->routeIs('karyawans.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-multiple"></i>
                                <span> Karyawan </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan'))
                        <li>
                            <a href="{{ route('location-change-requests.index') }}" class="waves-effect {{ request()->routeIs('location-change-requests.*') ? 'active' : '' }}">
                                <i class="mdi mdi-swap-horizontal"></i>
                                <span> Location Change </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan'))
                        <li class="has_sub {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="mdi mdi-calendar-check"></i> <span> Attendance </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="list-unstyled">
                                <li><a class="{{ request()->routeIs('attendance.checkin') ? 'active' : '' }}" href="{{ route('attendance.checkin') }}">Check In/Out</a></li>
                                @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi'))
                                <li><a class="{{ request()->routeIs('attendance.report') ? 'active' : '' }}" href="{{ route('attendance.report') }}">Report</a></li>
                                <li><a class="{{ request()->routeIs('attendance.absences') ? 'active' : '' }}" href="{{ route('attendance.absences') }}">Absences</a></li>
                                <li><a class="{{ request()->routeIs('attendance.recap') ? 'active' : '' }}" href="{{ route('attendance.recap') }}">Recap</a></li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan'))
                        <li>
                            <a href="{{ route('overtime.index') }}" class="waves-effect {{ request()->routeIs('overtime.*') ? 'active' : '' }}">
                                <i class="mdi mdi-timer-sand"></i>
                                <span> Overtime </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan'))
                        <li>
                            <a href="{{ route('reports.index') }}" class="waves-effect {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                                <i class="mdi mdi-clipboard-text"></i>
                                <span> Laporan </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasAnyRole(['Super Admin','Admin Lokasi']))
                        <li>
                            <a href="{{ route('work-recaps.index') }}" class="waves-effect {{ request()->routeIs('work-recaps.*') ? 'active' : '' }}">
                                <i class="mdi mdi-history"></i>
                                <span> Rekap Jam Kerja </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('work-targets.index') }}" class="waves-effect {{ request()->routeIs('work-targets.*') ? 'active' : '' }}">
                                <i class="mdi mdi-bullseye"></i>
                                <span> Target Jam Kerja </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi'))
                        <li>
                            @php
                                $today = now()->toDateString();
                                if ($authUser->hasRole('Super Admin')) {
                                    $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date','>=',$today)->count();
                                } else {
                                    $locId = $authUser->location_id;
                                    $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date','>=',$today)
                                        ->where(function($q) use ($locId){
                                            $q->where('is_national',true)
                                              ->orWhere(function($qq) use ($locId){ $qq->where('is_national',false)->where('location_id',$locId); });
                                        })->count();
                                }
                            @endphp
                            <a href="{{ route('holidays.index') }}" class="waves-effect {{ request()->routeIs('holidays.*') ? 'active' : '' }}">
                                <i class="mdi mdi-flag"></i>
                                <span> Holidays @if($upcomingHolidayCount>0)<span class="badge badge-info float-right">{{ $upcomingHolidayCount }}</span>@endif</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('weekly-offs.index') }}" class="waves-effect {{ request()->routeIs('weekly-offs.*') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar-remove"></i>
                                <span> Weekly Offs </span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan'))
                        <li>
                            @php
                                if ($authUser->hasRole('Super Admin')) {
                                    $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->count();
                                } elseif ($authUser->hasRole('Admin Lokasi')) {
                                    $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->where('location_id', $authUser->location_id)->count();
                                } else {
                                    $pendingLeavesCount = 0;
                                }
                            @endphp
                            <a href="{{ route('leaves.index') }}" class="waves-effect {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-alert"></i>
                                <span> Leaves @if($pendingLeavesCount>0 && ($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi')))<span class="badge badge-warning float-right">{{ $pendingLeavesCount }}</span>@endif</span>
                            </a>
                        </li>
                        @endif
                        @if($authUser->hasRole('Admin Lokasi'))
                        <li class="menu-title">Location</li>
                        <li>
                            <a href="{{ route('location-admin-tasks.index') }}" class="waves-effect {{ request()->routeIs('location-admin-tasks.*') ? 'active' : '' }}">
                                <i class="mdi mdi-map-marker"></i>
                                <span> Location Tasks </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('shifts.rosters.index') }}" class="waves-effect {{ request()->routeIs('shifts.rosters.*') ? 'active' : '' }}">
                                <i class="mdi mdi-calendar"></i>
                                <span> Weekly Rosters </span>
                            </a>
                        </li>
                        @if($authUser->location_id)
                        <li>
                            <a href="{{ route('locations.settings', $authUser->location_id) }}" class="waves-effect {{ request()->routeIs('locations.settings*') ? 'active' : '' }}">
                                <i class="mdi mdi-cog"></i>
                                <span> My Location Settings </span>
                            </a>
                        </li>
                        @endif
                        @endif
                        @if($authUser->hasRole('Super Admin'))
                        <li class="menu-title">Administration</li>
                        <li>
                            <a href="{{ route('users.index') }}" class="waves-effect {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-multiple"></i>
                                <span> Users </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('divisions.index') }}" class="waves-effect {{ request()->routeIs('divisions.*') ? 'active' : '' }}">
                                <i class="mdi mdi-vector-arrange-above"></i>
                                <span> Divisions </span>
                            </a>
                        </li>
                        <li class="has_sub {{ request()->routeIs('locations.*') || request()->routeIs('shifts.scheduler') || request()->routeIs('shifts.rosters.*') || request()->routeIs('location-shifts.*') ? 'active' : '' }}">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="mdi mdi-google-maps"></i> <span> Locations </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="list-unstyled">
                                <li><a class="{{ request()->routeIs('locations.index') || request()->routeIs('locations.show') || request()->routeIs('locations.edit') ? 'active' : '' }}" href="{{ route('locations.index') }}">All Locations</a></li>
                                @if($authUser->hasAnyRole(['Super Admin','Admin Lokasi']))
                                <li><a class="{{ request()->routeIs('shifts.rosters.*') ? 'active' : '' }}" href="{{ route('shifts.rosters.index') }}">Weekly Rosters</a></li>
                                @endif
                                <li><a class="{{ request()->routeIs('location-shifts.*') ? 'active' : '' }}" href="{{ route('location-shifts.index') }}">Location Shifts</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="{{ route('location-admins.index') }}" class="waves-effect {{ request()->routeIs('location-admins.*') ? 'active' : '' }}">
                                <i class="mdi mdi-account-cog"></i>
                                <span> Location Admins </span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- Left Sidebar End -->
        @endauth

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">
                @auth
                <!-- Top Bar Start -->
                @php
                    $unreadMessagesCount = $authUser ? \App\Models\Message::where('receiver_id', $authUser->id)->whereNull('read_at')->count() : 0;
                    $pendingOvertimeCount = $authUser ? \App\Models\OvertimeApproval::where('master_id', $authUser->id)->where('status', 'pending')->count() : 0;
                    $pendingReportCount = 0;
                    if ($authUser) {
                        if ($authUser->hasRole('Super Admin')) {
                            $pendingReportCount = \App\Models\ReportApproval::where('approver_role', 'super_admin')
                                ->where('status', 'pending')
                                ->count();
                        } elseif ($authUser->hasRole('Admin Lokasi')) {
                            $pendingReportCount = \App\Models\ReportApproval::where('approver_role', 'admin_lokasi')
                                ->where('status', 'pending')
                                ->where(function ($q) use ($authUser) {
                                    $q->whereNull('approver_id')->orWhere('approver_id', $authUser->id);
                                })
                                ->whereHas('report', function ($r) use ($authUser) {
                                    $r->where('location_id', $authUser->location_id)
                                        ->orWhere('assigned_admin_id', $authUser->id);
                                })
                                ->count();
                        }
                    }
                    $unreadNotificationCount = $authUser ? $authUser->unreadNotifications()->count() : 0;
                    $totalNotifications = $unreadMessagesCount + $pendingOvertimeCount + $pendingReportCount + $unreadNotificationCount;
                    $notificationLink = ($authUser && ($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi')))
                        ? route('tasks.progress.approvals')
                        : route('tasks.index');
                    $avatarPath = $authUser && $authUser->profile_photo_path ? str_replace('\\','/',$authUser->profile_photo_path) : null;
                    $avatarUrl = asset('assets/img/user2-160x160.jpg');
                    if ($avatarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($avatarPath)) {
                        $avatarUrl = asset('storage/' . ltrim($avatarPath, '/'));
                    }
                @endphp
                <div class="topbar">
                    <nav class="navbar-custom">
                        <ul class="list-inline float-right mb-0">
                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false">
                                    <i class="ti-email noti-icon"></i>
                                    @if($unreadMessagesCount > 0)
                                    <span class="badge badge-danger noti-icon-badge">{{ $unreadMessagesCount }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
                                    <div class="dropdown-item noti-title">
                                        <h5>Pesan</h5>
                                    </div>
                                    <a href="{{ route('messages.index') }}" class="dropdown-item notify-item">Lihat pesan</a>
                                </div>
                            </li>
                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false" id="notifDropdown">
                                    <i class="ti-bell noti-icon"></i>
                                    @if($totalNotifications > 0)
                                    <span class="badge badge-success noti-icon-badge">{{ $totalNotifications }}</span>
                                    @endif
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
                                    <div class="dropdown-item noti-title">
                                        <h5><span class="badge badge-danger float-right">{{ $totalNotifications }}</span>Notifikasi</h5>
                                    </div>
                                    <a href="{{ route('messages.index') }}" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-email mr-2"></i> Pesan baru</span>
                                        <span class="badge badge-light">{{ $unreadMessagesCount }}</span>
                                    </a>
                                    <a href="{{ route('reports.index') }}" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-file-document-box-check-outline mr-2"></i> Laporan menunggu</span>
                                        <span class="badge badge-light">{{ $pendingReportCount }}</span>
                                    </a>
                                    <a href="{{ route('overtime.index') }}" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-timeline-clock-outline mr-2"></i> Overtime menunggu</span>
                                        <span class="badge badge-light">{{ $pendingOvertimeCount }}</span>
                                    </a>
                                    <a href="{{ $notificationLink }}" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-bell-ring mr-2"></i> Notifikasi tugas/progres</span>
                                        <span class="badge badge-light">{{ $unreadNotificationCount }}</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="{{ $notificationLink }}" class="dropdown-item notify-item text-center">Lihat semua</a>
                                </div>
                            </li>
                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false">
                                    <img src="{{ $avatarUrl }}" alt="user" class="rounded-circle"> <span class="ml-1 d-none d-sm-inline-block">{{ $authUser->name ?? 'User' }} <i class="mdi mdi-chevron-down"></i> </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <div class="dropdown-item noti-title">
                                        <h5>{{ $authUser->name ?? 'User' }}</h5>
                                        <small>{{ $authUser ? $authUser->getRoleNames()->first() : '' }}</small>
                                    </div>
                                    @if($authUser)
                                    @php $twoFactorEnabled = $authUser->hasTwoFactorEnabled(); @endphp
                                    <a class="dropdown-item" href="{{ route('profile.show') }}"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profile</a>
                                    @if(!$twoFactorEnabled)
                                    <a class="dropdown-item" href="{{ route('2fa.setup') }}"><i class="mdi mdi-lock-check m-r-5 text-muted"></i> Enable Two-Factor</a>
                                    @else
                                    <a class="dropdown-item" href="{{ route('2fa.setup') }}"><i class="mdi mdi-lock-check m-r-5 text-muted"></i> Manage Two-Factor</a>
                                    <form action="{{ route('2fa.disable') }}" method="POST" class="px-3 py-1">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-block btn-sm">Disable 2FA</button>
                                    </form>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="px-3">
                                        @csrf
                                        <button type="submit" class="btn btn-primary btn-block">Logout</button>
                                    </form>
                                    @endif
                                </div>
                            </li>
                        </ul>
                        <ul class="list-inline menu-left mb-0">
                            <li class="float-left">
                                <button class="button-menu-mobile open-left waves-light waves-effect">
                                    <i class="mdi mdi-menu"></i>
                                </button>
                            </li>
                        </ul>
                        <div class="clearfix"></div>
                    </nav>
                </div>
                <!-- Top Bar End -->
                @endauth

                <div class="page-content-wrapper">
                    @hasSection('title')
                        <div class="container-fluid">
                            @yield('title')
                        </div>
                    @endif
                    <div class="container-fluid">
                        @yield('content')
                    </div>
                </div>
            </div>

            <footer class="footer">
                 {{ date('Y') }} {{ $brandName }}.
            </footer>
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <script src="{{ asset('NewAsset/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/modernizr.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/detect.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/fastclick.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.blockUI.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/waves.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/plugins/skycons/skycons.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/plugins/raphael/raphael-min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/plugins/morris/morris.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/pages/dashborad.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notifDropdown = document.getElementById('notifDropdown');
            if (notifDropdown) {
                notifDropdown.addEventListener('click', function () {
                    fetch("{{ route('notifications.read') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    }).catch(() => {});
                }, { once: true });
            }
        });
    </script>
    @unless(request()->routeIs('messages.*'))
    @if(session('forbidden'))
    <script>
        Swal.fire({ icon: 'error', title: 'Akses ditolak', text: @json(session('forbidden')), confirmButtonText: 'OK' });
    </script>
    @endif
    @if(session('success'))
    <script>
        Swal.fire({ icon: 'success', title: 'Berhasil', text: @json(session('success')), timer: 2200, showConfirmButton: false });
    </script>
    @endif
    @if(session('error'))
    <script>
        Swal.fire({ icon: 'error', title: 'Gagal', text: @json(session('error')), confirmButtonText: 'OK' });
    </script>
    @endif
    @if(session('warning'))
    <script>
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: @json(session('warning')), confirmButtonText: 'OK' });
    </script>
    @endif
    @if(session('info'))
    <script>
        Swal.fire({ icon: 'info', title: 'Informasi', text: @json(session('info')), confirmButtonText: 'OK' });
    </script>
    @endif
    @if(session('status'))
    <script>
        Swal.fire({ icon: 'info', title: 'Status', text: @json(session('status')), confirmButtonText: 'OK' });
    </script>
    @endif
    @endunless
    @if($brandLocation && $brandLocation->custom_js_url)
    <script src="{{ $brandLocation->custom_js_url }}"></script>
    @endif
</body>
</html>
