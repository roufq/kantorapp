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
    
    <link rel="stylesheet" href="{{ asset('NewAsset/assets/css/bootstrap.min.css') }}" type="text/css">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">

    <style>
        :root {
            --primary: #10b981;
            --primary-light: #ecfdf5;
            --sidebar-bg: #ffffff;
            --body-bg: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --card-radius: 20px;
            --sidebar-width: 280px;

            /* Soft Pastel Palette - Modern Enterprise */
            --soft-mint: #f0fdf4;
            --soft-mint-text: #166534;
            --soft-sky: #f0f9ff;
            --soft-sky-text: #075985;
            --soft-indigo: #f5f3ff;
            --soft-indigo-text: #5b21b6;
            --soft-rose: #fff1f2;
            --soft-rose-text: #9f1239;
            --soft-honey: #fffdf2;
            --soft-honey-text: #854d0e;
            --soft-slate: #f1f5f9;
            --soft-slate-text: #334155;
        }

        body {
            background-color: var(--body-bg) !important;
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            color: var(--text-main);
            overflow-x: hidden;
            min-height: 100vh;
        }

        #wrapper { display: flex; min-height: 100vh; }

        /* ======== SIDEBAR ======== */
        .side-menu {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            padding: 20px 10px;
            display: flex;
            flex-direction: column;
            box-shadow: 20px 0 60px rgba(0,0,0,0.02);
            transition: all 0.3s ease;
            overflow-y: auto; /* Fix scrollability */
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }

        /* Webkit scrollbar for sidebar */
        .side-menu::-webkit-scrollbar { width: 4px; }
        .side-menu::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

        .logo-box { padding: 0 20px 40px; }
        .logo-box .brand-label { font-size: 1.5rem; font-weight: 800; color: var(--text-main); letter-spacing: -0.5px; }
        .logo-box .logo-icon { color: var(--primary); font-size: 2rem; margin-right: 10px; }

        .menu-list { list-style: none; padding: 0; margin: 0; flex-grow: 1; }
        .menu-list li { margin-bottom: 8px; }
        .menu-list a {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 20px;
            transition: all 0.2s ease;
        }
        .menu-list a i { font-size: 1.4rem; margin-right: 16px; opacity: 0.7; }
        .menu-list a:hover { color: var(--primary); background: #f8fafc; }
        .menu-list a.active {
            background: #e0f2f1 !important; /* Soft mint active bg */
            color: #0d9488 !important;
        }
        .menu-list a.active i { opacity: 1; }

        /* ======== CONTENT ======== */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 2rem 3rem; 
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        /* ======== MOBILE HEADER ======== */
        .mobile-header {
            display: none;
            background: #fff;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            align-items: center;
            justify-content: space-between;
        }

        /* ======== TOP NAVIGATION ======== */
        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .greeting h1 { font-size: clamp(1.5rem, 4vw, 2.2rem); font-weight: 800; margin-bottom: 4px; letter-spacing: -0.5px; }
        .greeting p { color: var(--text-muted); font-weight: 500; font-size: clamp(0.9rem, 2vw, 1.05rem); }

        .top-actions { display: flex; align-items: center; gap: 15px; }
        .search-pill {
            background: #ffffff;
            border-radius: 40px;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            width: 280px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s ease;
        }
        .search-pill input { border: none; outline: none; background: transparent; width: 100%; margin-left: 10px; font-weight: 500; color: var(--text-main); }
        .search-pill i { color: var(--text-muted); font-size: 1rem; }

        .nav-icon-btn {
            width: 48px; height: 48px;
            background: #ffffff;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            color: var(--text-main); font-size: 1.2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: relative; cursor: pointer; border: 1px solid rgba(0,0,0,0.02);
        }
        .nav-icon-btn .badge-dot {
            position: absolute; top: 12px; right: 12px;
            width: 8px; height: 8px;
            background: #ef4444; border-radius: 50%; border: 2px solid #fff;
        }

        .user-profile-img {
            width: 48px; height: 48px;
            border-radius: 16px; object-fit: cover;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(0,0,0,0.02);
            cursor: pointer;
        }

        /* ======== GLOBAL SOFT THEME ======== */
        .page-body h1, .page-body h2, .page-body h3, .page-body h4, .page-body h5, .page-body h6 {
            color: var(--text-main);
            font-weight: 700;
        }

        .card {
            background: #ffffff;
            border-radius: var(--card-radius) !important;
            border: 1px solid #f1f5f9 !important; /* Defined soft border */
            box-shadow: 0 5px 25px rgba(0,0,0,0.02) !important;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: transparent !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 24px 30px !important;
        }
        .card-title { font-weight: 800 !important; font-size: 1.15rem !important; color: var(--text-main) !important; }
        .card-body { padding: 30px !important; }

        /* Table Resets */
        table { width: 100% !important; }
        .table thead th {
            background: #f8fafc !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #94a3b8 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.5px !important;
            padding: 18px 24px !important;
        }
        .table tbody td {
            padding: 18px 24px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f8fafc !important;
            color: #475569 !important;
            font-size: 0.95rem !important;
            font-weight: 500 !important;
        }
        .table tbody tr:hover { background-color: #f8fafc !important; }

        /* Badges */
        .badge {
            padding: 0.6rem 1rem !important;
            border-radius: 12px !important;
            font-weight: 700 !important;
            font-size: 0.72rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            border: none !important;
            display: inline-flex;
            align-items: center;
        }
        .badge-info { background: var(--soft-sky) !important; color: var(--soft-sky-text) !important; }
        .badge-success { background: var(--soft-mint) !important; color: var(--soft-mint-text) !important; }
        .badge-warning { background: var(--soft-honey) !important; color: var(--soft-honey-text) !important; }
        .badge-danger { background: var(--soft-rose) !important; color: var(--soft-rose-text) !important; }
        .badge-primary { background: var(--soft-indigo) !important; color: var(--soft-indigo-text) !important; }
        .badge-secondary { background: var(--soft-slate) !important; color: var(--soft-slate-text) !important; }

        /* Buttons */
        .btn-primary { background: var(--primary) !important; border-color: var(--primary) !important; padding: 12px 28px !important; font-weight: 700 !important; letter-spacing: -0.2px !important; transition: all 0.3s ease !important; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2) !important; }
        
        .btn-light { background: #f8fafc !important; border: 1px solid #f1f5f9 !important; font-weight: 600 !important; color: var(--text-muted) !important; }
        .btn-light:hover { background: #f1f5f9 !important; color: var(--text-main) !important; }

        .form-control, .form-select {
            border-radius: 14px !important;
            padding: 10px 18px !important; /* Slightly reduced top/bottom padding */
            border: 1px solid #e2e8f0 !important;
            background: #f8fafc !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
            line-height: 1.6 !important; /* Ensure vertical space for text */
            height: auto !important;
        }
        .form-control:focus { background: #fff !important; border-color: var(--primary) !important; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1) !important; }

        /* ======== DASHBOARD & COMPONENT SPECIALS ======== */
        .glass-card {
            background: #ffffff;
            border-radius: 32px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.02);
            border: 1px solid rgba(255,255,255,0.8);
            height: 100%;
        }
        .stat-value { font-size: 2.8rem; font-weight: 800; line-height: 1.2; }
        .stat-label { font-weight: 500; color: var(--text-muted); font-size: 1.1rem; }
        .stat-change { font-size: 0.9rem; font-weight: 700; margin-top: 8px; }
        .stat-change.up { color: #10b981; }

        .activity-item { display: flex; align-items: center; gap: 16px; margin-bottom: 20px; }
        .activity-icon {
            width: 48px; height: 48px;
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .activity-info h4 { font-size: 1rem; font-weight: 700; margin: 0; }
        .activity-info p { margin: 0; font-size: 0.9rem; color: var(--text-muted); font-weight: 500; }

        /* Responsive Mobile Drawer */
        @media (max-width: 1100px) {
            .side-menu { transform: translateX(-100%); width: var(--sidebar-width); pointer-events: none; opacity: 0; }
            .side-menu.active { transform: translateX(0); pointer-events: all; opacity: 1; height: 100vh; }
            .main-content { margin-left: 0; padding: 1.5rem; }
            .mobile-header { display: flex; }
            .top-nav { display: none; }
            .search-pill { display: none; }
        }

        /* Generic Table Wrapper */
        .table-responsive { border-radius: 12px; border: none; overflow-x: auto; }

        /* Dropdown Polish */
        .hide-caret::after { display: none !important; }
        .dropdown-menu { animation: dropdownFadeIn 0.2s ease-out; }
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-item:hover {
            background-color: var(--primary-light) !important;
            color: var(--primary) !important;
        }
        .dropdown-menu.show { display: block !important; }
        .top-actions .dropdown-menu {
            right: 0 !important;
            left: auto !important;
            margin-top: 15px !important;
            transform-origin: top right;
        }
        .bg-soft-success { background-color: #ecfdf5 !important; }
        .bg-soft-info { background-color: #f0f9ff !important; }
        .bg-soft-warning { background-color: #fffbeb !important; }
        .bg-soft-danger { background-color: #fef2f2 !important; }
        .rounded-4 { border-radius: 12px !important; }
    </style>
    @stack('styles')
</head>
<body>
    <div id="wrapper">
        @auth
        <!-- Sidebar -->
        <div class="side-menu">
            <div class="logo-box d-flex align-items-center">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo-img" style="width: 40px; height: 40px; margin-right: 12px; border-radius: 10px;">
                <span class="brand-label">KantorApp</span>
            </div>
            
            <ul class="menu-list">
                <li class="menu-title" style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">DASHBOARD & CHATS</li>
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="mdi mdi-home-outline"></i> <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                <li>
                    <a href="{{ route('messages.index') }}" class="{{ request()->routeIs('messages.*') ? 'active' : '' }}">
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
                    <a href="{{ route('tasks.progress.approvals') }}" class="{{ request()->routeIs('tasks.progress.approvals') ? 'active' : '' }}">
                        <i class="mdi mdi-shield-check-outline"></i> 
                        <span>{{ __('Task Approvals') }} @if($pendingCreationCount>0)<span class="badge rounded-pill bg-warning text-dark ms-2">{{ $pendingCreationCount }}</span>@endif</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('shifts.rosters.calendar') }}" class="{{ request()->routeIs('shifts.rosters.calendar') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-blank-multiple"></i> <span>{{ __('Calendar') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                <li>
                    <a href="{{ route('employees.index') }}" class="{{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="mdi mdi-account-group-outline"></i> <span>{{ __('Employees') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                <li>
                    <a href="{{ route('location-change-requests.index') }}" class="{{ request()->routeIs('location-change-requests.*') ? 'active' : '' }}">
                        <i class="mdi mdi-map-marker-distance"></i> <span>{{ __('Location Change') }}</span>
                    </a>
                </li>
                @endif

                <li class="menu-title" style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">TIME & ATTENDANCE</li>
                <li>
                    <a href="{{ route('attendance.checkin') }}" class="{{ request()->routeIs('attendance.checkin') ? 'active' : '' }}">
                        <i class="mdi mdi-clock-check-outline"></i> <span>{{ __('Check In/Out') }}</span>
                    </a>
                </li>
                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                <li>
                    <a href="{{ route('attendance.report') }}" class="{{ request()->routeIs('attendance.report') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-search"></i> <span>{{ __('Attendance Report') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('attendance.absences') }}" class="{{ request()->routeIs('attendance.absences') ? 'active' : '' }}">
                        <i class="mdi mdi-account-alert-outline"></i> <span>{{ __('Absences') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('attendance.recap') }}" class="{{ request()->routeIs('attendance.recap') ? 'active' : '' }}">
                        <i class="mdi mdi-clipboard-text-play-outline"></i> <span>{{ __('Recap') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                <li>
                    <a href="{{ route('overtime.index') }}" class="{{ request()->routeIs('overtime.*') ? 'active' : '' }}">
                        <i class="mdi mdi-progress-clock"></i> <span>{{ __('Overtime') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="mdi mdi-clipboard-text-outline"></i> <span>{{ __('Reports') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                <li>
                    <a href="{{ route('work-recaps.index') }}" class="{{ request()->routeIs('work-recaps.*') ? 'active' : '' }}">
                        <i class="mdi mdi-timer-sand"></i> <span>{{ __('Working Hours Recap') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('work-targets.index') }}" class="{{ request()->routeIs('work-targets.*') ? 'active' : '' }}">
                        <i class="mdi mdi-bullseye-arrow"></i> <span>{{ __('Target Working Hours') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin']))
                @php
                    $today = now()->toDateString();
                    if ($authUser->hasRole('Super Admin')) {
                        $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date','>=',$today)->count();
                    } else {
                        $locId = $authUser->location_id;
                        $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date','>=',$today)
                        ->where(function($q) use ($locId){
                            $q->where('is_national',true)->orWhere(function($qq) use ($locId){ $qq->where('is_national',false)->where('location_id',$locId); });
                        })->count();
                    }
                @endphp
                <li>
                    <a href="{{ route('holidays.index') }}" class="{{ request()->routeIs('holidays.*') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-star-outline"></i> 
                        <span>{{ __('Holidays') }} @if($upcomingHolidayCount>0)<span class="badge rounded-pill bg-info text-dark ms-2">{{ $upcomingHolidayCount }}</span>@endif</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('weekly-offs.index') }}" class="{{ request()->routeIs('weekly-offs.*') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-x"></i> <span>{{ __('Weekly Offs') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee']))
                @php
                    if ($authUser->hasRole('Super Admin')) {
                        $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->count();
                    } elseif ($authUser->hasRole('Location Admin')) {
                        $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->where('location_id', $authUser->location_id)->count();
                    } else {
                        $pendingLeavesCount = 0;
                    }
                @endphp
                <li>
                    <a href="{{ route('leaves.index') }}" class="{{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                        <i class="mdi mdi-account-arrow-right-outline"></i> 
                        <span>{{ __('Leaves') }} @if($pendingLeavesCount>0 && ($authUser->hasRole('Super Admin') || $authUser->hasRole('Location Admin')))<span class="badge rounded-pill bg-warning text-dark ms-2">{{ $pendingLeavesCount }}</span>@endif</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasRole('Location Admin'))
                <li class="menu-title" style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">LOCATION ADMIN</li>
                <li>
                    <a href="{{ route('location-admin-tasks.index') }}" class="{{ request()->routeIs('location-admin-tasks.*') ? 'active' : '' }}">
                        <i class="mdi mdi-map-marker-radius"></i> <span>{{ __('Location Tasks') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('shifts.rosters.index') }}" class="{{ request()->routeIs('shifts.rosters.*') ? 'active' : '' }}">
                        <i class="mdi mdi-calendar-clock-outline"></i> <span>{{ __('Weekly Rosters') }}</span>
                    </a>
                </li>
                @if($authUser->location_id)
                <li>
                    <a href="{{ route('locations.settings', $authUser->location_id) }}" class="{{ request()->routeIs('locations.settings*') ? 'active' : '' }}">
                        <i class="mdi mdi-office-building-cog-outline"></i> <span>{{ __('My Location Settings') }}</span>
                    </a>
                </li>
                @endif
                @endif

                @if($authUser->hasAnyRole(['Super Admin', 'HR']))
                <li class="menu-title" style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">TALENT & HR</li>
                <li>
                    <a href="{{ route('jobdesks.index') }}" class="{{ request()->routeIs('jobdesks.*') ? 'active' : '' }}">
                        <i class="mdi mdi-briefcase-variant-outline"></i> <span>{{ __('Jobdesk & Catalog') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('jobdesk-targets.index') }}" class="{{ request()->routeIs('jobdesk-targets.*') ? 'active' : '' }}">
                        <i class="mdi mdi-bullseye-arrow"></i> <span>{{ __('Target Output') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('approval-rules.index') }}" class="{{ request()->routeIs('approval-rules.*') ? 'active' : '' }}">
                        <i class="mdi mdi-playlist-check"></i> <span>{{ __('Approval Rules') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('employee-positions.index') }}" class="{{ request()->routeIs('employee-positions.*') ? 'active' : '' }}">
                        <i class="mdi mdi-account-tie-outline"></i> <span>{{ __('Position History') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('employee-transfers.index') }}" class="{{ request()->routeIs('employee-transfers.*') ? 'active' : '' }}">
                        <i class="mdi mdi-account-switch-outline"></i> <span>{{ __('Transfers') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('employee-contracts.index') }}" class="{{ request()->routeIs('employee-contracts.*') ? 'active' : '' }}">
                        <i class="mdi mdi-file-certificate-outline"></i> <span>{{ __('Contracts') }}</span>
                    </a>
                </li>
                @endif

                @if($authUser->hasRole('Super Admin'))
                <li class="menu-title" style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">ADMINISTRATION</li>
                <li>
                    <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <i class="mdi mdi-badge-account-horizontal-outline"></i> <span>{{ __('Users') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('divisions.index') }}" class="{{ request()->routeIs('divisions.*') ? 'active' : '' }}">
                        <i class="mdi mdi-layers-triple-outline"></i> <span>{{ __('Divisions') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('locations.index') }}" class="{{ request()->routeIs('locations.index') || request()->routeIs('locations.show') || request()->routeIs('locations.edit') ? 'active' : '' }}">
                        <i class="mdi mdi-map-search-outline"></i> <span>{{ __('All Locations') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('location-shifts.index') }}" class="{{ request()->routeIs('location-shifts.*') ? 'active' : '' }}">
                        <i class="mdi mdi-clock-fast"></i> <span>{{ __('Location Shifts') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('location-admins.index') }}" class="{{ request()->routeIs('location-admins.*') ? 'active' : '' }}">
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
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" style="width: 32px; height: 32px; margin-right: 10px; border-radius: 8px;">
                    <span class="fw-bold fs-4">KantorApp</span>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <i class="mdi mdi-bell-outline fs-4 text-muted"></i>
                    <a href="{{ route('profile.show') }}">
                        <img src="{{ $authUser->profile_photo_path ? asset('storage/' . $authUser->profile_photo_path) : asset('assets/img/user2-160x160.jpg') }}" class="rounded-pill" style="width: 32px; height: 32px;" alt="">
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
                        <div class="nav-icon-btn dropdown-toggle hide-caret" id="notifDropdown" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="mdi mdi-bell-outline"></i>
                            @if($unreadNotificationCount > 0)
                                <div class="badge-dot"></div>
                            @endif
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notifDropdown" style="border-radius: 20px; min-width: 320px; padding: 15px; z-index: 1060; margin-top: 20px !important;">
                            <li class="px-3 py-2 border-bottom mb-2 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold">{{ __('Notifications') }}</h6>
                                <span class="badge bg-soft-success text-success">{{ $unreadNotificationCount }} New</span>
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
                                                <div class="activity-icon bg-soft-info text-info me-3" style="width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;">
                                                    <i class="mdi mdi-calendar-check fs-6"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <p class="mb-0 fw-bold text-truncate" style="font-size: 0.85rem;">{{ $title }}</p>
                                                    <small class="text-muted" style="font-size: 0.75rem;">{{ $notif->created_at->diffForHumans() }}</small>
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
                            <li><hr class="dropdown-divider mx-2 opacity-10"></li>
                            <li>
                                <a class="dropdown-item text-center fw-bold py-2 text-primary" href="{{ route('tasks.index') }}">
                                    {{ __('View All Notifications') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    @php
                        $avatarUrl = $authUser->profile_photo_path ? asset('storage/' . $authUser->profile_photo_path) : asset('assets/img/user2-160x160.jpg');
                    @endphp
                    <div class="dropdown">
                        <button class="btn p-0 border-0 dropdown-toggle hide-caret" type="button" id="userDropdown" data-toggle="dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ $avatarUrl }}" class="user-profile-img" alt="Profile" style="cursor: pointer;">
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown" style="border-radius: 20px; min-width: 260px; padding: 15px; z-index: 1060; margin-top: 20px !important;">
                            <li class="px-3 py-3 border-bottom mb-2">
                                <div class="d-flex align-items-center mb-1">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <h6 class="mb-0 fw-bold text-truncate" style="font-size: 1rem; max-width: 150px;">{{ $authUser->name }}</h6>
                                        <small class="text-muted fw-semibold" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $authUser->getRoleNames()->first() }}</small>
                                    </div>
                                    <div class="badge bg-soft-success text-success rounded-pill px-2 py-1" style="font-size: 0.7rem;">Active</div>
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 px-3 rounded-pill fw-medium mb-1 mt-1" href="{{ route('profile.show') }}">
                                    <i class="mdi mdi-account-circle-outline me-2 fs-5 opacity-75"></i> {{ __('My Profile') }}
                                </a>
                            </li>
                            @php $twoFactorEnabled = $authUser->hasTwoFactorEnabled(); @endphp
                            <li>
                                <a class="dropdown-item py-2 px-3 rounded-pill fw-medium mb-1" href="{{ route('2fa.setup') }}">
                                    <i class="mdi mdi-shield-link-variant-outline me-2 fs-5 opacity-75"></i> {{ $twoFactorEnabled ? __('Security Settings') : __('Enable 2FA') }}
                                </a>
                            </li>
                            <li><hr class="dropdown-divider mx-2 opacity-10"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 px-3 rounded-pill fw-bold text-danger">
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
    <script src="{{ asset('NewAsset/assets/js/jquery.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="{{ asset('NewAsset/assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('NewAsset/assets/js/app.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('mobile-toggle');
            const sidebar = document.querySelector('.side-menu');
            if (toggle && sidebar) {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebar.classList.toggle('active');
                });
                document.addEventListener('click', function(e) {
                    if (!sidebar.contains(e.target) && sidebar.classList.contains('active')) {
                        sidebar.classList.remove('active');
                    }
                });
            }

            // Manual Header Dropdowns Toggle
            $('.top-actions .dropdown-toggle').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const $target = $(this).next('.dropdown-menu');
                $('.dropdown-menu').not($target).removeClass('show'); // Close others
                $target.toggleClass('show');
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu').removeClass('show');
                }
            });
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>
</html>
