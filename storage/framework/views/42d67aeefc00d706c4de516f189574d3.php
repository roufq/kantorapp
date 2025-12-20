<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php
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
    ?>
    <title><?php echo e($brandName); ?> | Portal</title>
    <meta content="Admin Dashboard" name="description">
    <meta content="Mannatthemes" name="author">

    <link rel="shortcut icon" href="<?php echo e(asset('NewAsset/assets/images/favicon.ico')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('NewAsset/assets/plugins/morris/morris.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('NewAsset/assets/css/bootstrap.min.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('NewAsset/assets/css/icons.css')); ?>" type="text/css">
    <link rel="stylesheet" href="<?php echo e(asset('NewAsset/assets/css/style.css')); ?>" type="text/css">
    <!-- Extra icon CDN to ensure all mdi icons are available -->
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" crossorigin="anonymous">
    <?php if($primaryColor || $secondaryColor): ?>
    <style>
        :root {
            <?php if($primaryColor): ?> --brand-primary: <?php echo e($primaryColor); ?>; <?php endif; ?>
            <?php if($secondaryColor): ?> --brand-secondary: <?php echo e($secondaryColor); ?>; <?php endif; ?>
        }
        .bg-primary { background-color: var(--brand-primary, #3c4ccf) !important; }
        .text-primary { color: var(--brand-primary, #3c4ccf) !important; }
        .btn-primary { background-color: var(--brand-primary, #3c4ccf); border-color: var(--brand-primary, #3c4ccf); }
        .badge-primary { background-color: var(--brand-primary, #3c4ccf); color: #fff; }
    </style>
    <?php endif; ?>
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
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body class="fixed-left">
    <!-- Loader -->
    <div id="preloader">
        <div id="status"><div class="spinner"></div></div>
    </div>

    <div id="wrapper">
        <?php if(auth()->guard()->check()): ?>
        <!-- ========== Left Sidebar Start ========== -->
        <div class="left side-menu">
            <button type="button" class="button-menu-mobile button-menu-mobile-topbar open-left waves-effect">
                <i class="ion-close"></i>
            </button>
            <div class="topbar-left">
                <div class="text-center">
                    <a href="<?php echo e(route('dashboard')); ?>" class="logo">
                        <?php if($brandLogoUrl): ?>
                            <img src="<?php echo e($brandLogoUrl); ?>" alt="<?php echo e($brandName); ?>" height="26">
                        <?php else: ?>
                            <i class="mdi mdi-assistant"></i> <?php echo e($brandName); ?>

                        <?php endif; ?>
                    </a>
                </div>
            </div>
            <div class="sidebar-inner slimscrollleft">
                <div id="sidebar-menu">
                    <ul>
                        <li class="menu-title">Main</li>
                        <li>
                            <a href="<?php echo e(route('dashboard')); ?>" class="waves-effect <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                                <i class="mdi mdi-airplay"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan')): ?>
                        <li>
                            <a href="<?php echo e(route('messages.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('messages.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-email"></i>
                                <span> Messages </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo e(route('tasks.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('tasks.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-clipboard-text-outline"></i>
                                <span> Tasks </span>
                            </a>
                        </li>
                        <?php if($authUser->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
                        <li>
                            <?php
                                $pendingCreationCount = \App\Models\Task::where('requires_approval', true)
                                    ->where('approval_status', 'pending')
                                    ->when($authUser->hasRole('Admin Lokasi'), function ($q) use ($authUser) {
                                        $q->where('approval_level', 'location_admin')
                                          ->whereHas('assignee', function ($qq) use ($authUser) {
                                              $qq->where('location_id', $authUser->location_id);
                                          });
                                    })
                                    ->count();
                            ?>
                            <a href="<?php echo e(route('tasks.progress.approvals')); ?>" class="waves-effect <?php echo e(request()->routeIs('tasks.progress.approvals') ? 'active' : ''); ?>">
                                <i class="mdi mdi-check-decagram"></i>
                                <span> Approval Tugas <?php if($pendingCreationCount>0): ?><span class="badge badge-warning float-right"><?php echo e($pendingCreationCount); ?></span><?php endif; ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
                        <li>
                            <a href="<?php echo e(route('shifts.rosters.calendar')); ?>" class="waves-effect <?php echo e(request()->routeIs('shifts.rosters.calendar') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-clock"></i>
                                <span> Kalender </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi')): ?>
                        <li>
                            <a href="<?php echo e(route('karyawans.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('karyawans.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-multiple"></i>
                                <span> Karyawan </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan')): ?>
                        <li>
                            <a href="<?php echo e(route('location-change-requests.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('location-change-requests.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-swap-horizontal"></i>
                                <span> Location Change </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan')): ?>
                        <li class="has_sub <?php echo e(request()->routeIs('attendance.*') ? 'active' : ''); ?>">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="mdi mdi-calendar-check"></i> <span> Attendance </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="list-unstyled">
                                <li><a class="<?php echo e(request()->routeIs('attendance.checkin') ? 'active' : ''); ?>" href="<?php echo e(route('attendance.checkin')); ?>">Check In/Out</a></li>
                                <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi')): ?>
                                <li><a class="<?php echo e(request()->routeIs('attendance.report') ? 'active' : ''); ?>" href="<?php echo e(route('attendance.report')); ?>">Report</a></li>
                                <li><a class="<?php echo e(request()->routeIs('attendance.absences') ? 'active' : ''); ?>" href="<?php echo e(route('attendance.absences')); ?>">Absences</a></li>
                                <li><a class="<?php echo e(request()->routeIs('attendance.recap') ? 'active' : ''); ?>" href="<?php echo e(route('attendance.recap')); ?>">Recap</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan')): ?>
                        <li>
                            <a href="<?php echo e(route('overtime.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('overtime.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-timer-sand"></i>
                                <span> Overtime </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan')): ?>
                        <li>
                            <a href="<?php echo e(route('reports.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-clipboard-text"></i>
                                <span> Laporan </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
                        <li>
                            <a href="<?php echo e(route('work-recaps.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('work-recaps.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-history"></i>
                                <span> Rekap Jam Kerja </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('work-targets.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('work-targets.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-bullseye"></i>
                                <span> Target Jam Kerja </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi')): ?>
                        <li>
                            <?php
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
                            ?>
                            <a href="<?php echo e(route('holidays.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('holidays.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-flag"></i>
                                <span> Holidays <?php if($upcomingHolidayCount>0): ?><span class="badge badge-info float-right"><?php echo e($upcomingHolidayCount); ?></span><?php endif; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('weekly-offs.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('weekly-offs.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-remove"></i>
                                <span> Weekly Offs </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi') || $authUser->hasRole('Karyawan')): ?>
                        <li>
                            <?php
                                if ($authUser->hasRole('Super Admin')) {
                                    $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->count();
                                } elseif ($authUser->hasRole('Admin Lokasi')) {
                                    $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->where('location_id', $authUser->location_id)->count();
                                } else {
                                    $pendingLeavesCount = 0;
                                }
                            ?>
                            <a href="<?php echo e(route('leaves.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('leaves.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-alert"></i>
                                <span> Leaves <?php if($pendingLeavesCount>0 && ($authUser->hasRole('Super Admin') || $authUser->hasRole('Admin Lokasi'))): ?><span class="badge badge-warning float-right"><?php echo e($pendingLeavesCount); ?></span><?php endif; ?></span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Admin Lokasi')): ?>
                        <li class="menu-title">Location</li>
                        <li>
                            <a href="<?php echo e(route('location-admin-tasks.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('location-admin-tasks.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-map-marker"></i>
                                <span> Location Tasks </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('shifts.rosters.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar"></i>
                                <span> Weekly Rosters </span>
                            </a>
                        </li>
                        <?php if($authUser->location_id): ?>
                        <li>
                            <a href="<?php echo e(route('locations.settings', $authUser->location_id)); ?>" class="waves-effect <?php echo e(request()->routeIs('locations.settings*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-cog"></i>
                                <span> My Location Settings </span>
                            </a>
                        </li>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if($authUser->hasRole('Super Admin')): ?>
                        <li class="menu-title">Administration</li>
                        <li>
                            <a href="<?php echo e(route('users.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-multiple"></i>
                                <span> Users </span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('divisions.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('divisions.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-vector-arrange-above"></i>
                                <span> Divisions </span>
                            </a>
                        </li>
                        <li class="has_sub <?php echo e(request()->routeIs('locations.*') || request()->routeIs('shifts.scheduler') || request()->routeIs('shifts.rosters.*') || request()->routeIs('location-shifts.*') ? 'active' : ''); ?>">
                            <a href="javascript:void(0);" class="waves-effect">
                                <i class="mdi mdi-google-maps"></i> <span> Locations </span> <span class="float-right"><i class="mdi mdi-chevron-right"></i></span>
                            </a>
                            <ul class="list-unstyled">
                                <li><a class="<?php echo e(request()->routeIs('locations.index') || request()->routeIs('locations.show') || request()->routeIs('locations.edit') ? 'active' : ''); ?>" href="<?php echo e(route('locations.index')); ?>">All Locations</a></li>
                                <?php if($authUser->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
                                <li><a class="<?php echo e(request()->routeIs('shifts.rosters.*') ? 'active' : ''); ?>" href="<?php echo e(route('shifts.rosters.index')); ?>">Weekly Rosters</a></li>
                                <?php endif; ?>
                                <li><a class="<?php echo e(request()->routeIs('location-shifts.*') ? 'active' : ''); ?>" href="<?php echo e(route('location-shifts.index')); ?>">Location Shifts</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo e(route('location-admins.index')); ?>" class="waves-effect <?php echo e(request()->routeIs('location-admins.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-cog"></i>
                                <span> Location Admins </span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        <!-- Left Sidebar End -->
        <?php endif; ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <div class="content">
                <?php if(auth()->guard()->check()): ?>
                <!-- Top Bar Start -->
                <?php
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
                ?>
                <div class="topbar">
                    <nav class="navbar-custom">
                        <ul class="list-inline float-right mb-0">
                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false">
                                    <i class="ti-email noti-icon"></i>
                                    <?php if($unreadMessagesCount > 0): ?>
                                    <span class="badge badge-danger noti-icon-badge"><?php echo e($unreadMessagesCount); ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
                                    <div class="dropdown-item noti-title">
                                        <h5>Pesan</h5>
                                    </div>
                                    <a href="<?php echo e(route('messages.index')); ?>" class="dropdown-item notify-item">Lihat pesan</a>
                                </div>
                            </li>
                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false" id="notifDropdown">
                                    <i class="ti-bell noti-icon"></i>
                                    <?php if($totalNotifications > 0): ?>
                                    <span class="badge badge-success noti-icon-badge"><?php echo e($totalNotifications); ?></span>
                                    <?php endif; ?>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-arrow dropdown-menu-lg">
                                    <div class="dropdown-item noti-title">
                                        <h5><span class="badge badge-danger float-right"><?php echo e($totalNotifications); ?></span>Notifikasi</h5>
                                    </div>
                                    <a href="<?php echo e(route('messages.index')); ?>" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-email mr-2"></i> Pesan baru</span>
                                        <span class="badge badge-light"><?php echo e($unreadMessagesCount); ?></span>
                                    </a>
                                    <a href="<?php echo e(route('reports.index')); ?>" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-file-document-box-check-outline mr-2"></i> Laporan menunggu</span>
                                        <span class="badge badge-light"><?php echo e($pendingReportCount); ?></span>
                                    </a>
                                    <a href="<?php echo e(route('overtime.index')); ?>" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-timeline-clock-outline mr-2"></i> Overtime menunggu</span>
                                        <span class="badge badge-light"><?php echo e($pendingOvertimeCount); ?></span>
                                    </a>
                                    <a href="<?php echo e($notificationLink); ?>" class="dropdown-item notify-item d-flex justify-content-between align-items-center">
                                        <span><i class="mdi mdi-bell-ring mr-2"></i> Notifikasi tugas/progres</span>
                                        <span class="badge badge-light"><?php echo e($unreadNotificationCount); ?></span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a href="<?php echo e($notificationLink); ?>" class="dropdown-item notify-item text-center">Lihat semua</a>
                                </div>
                            </li>
                            <li class="list-inline-item dropdown notification-list">
                                <a class="nav-link dropdown-toggle arrow-none waves-effect nav-user" data-toggle="dropdown" href="#" role="button"
                                   aria-haspopup="false" aria-expanded="false">
                                    <img src="<?php echo e($avatarUrl); ?>" alt="user" class="rounded-circle"> <span class="ml-1 d-none d-sm-inline-block"><?php echo e($authUser->name ?? 'User'); ?> <i class="mdi mdi-chevron-down"></i> </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right profile-dropdown ">
                                    <div class="dropdown-item noti-title">
                                        <h5><?php echo e($authUser->name ?? 'User'); ?></h5>
                                        <small><?php echo e($authUser ? $authUser->getRoleNames()->first() : ''); ?></small>
                                    </div>
                                    <?php if($authUser): ?>
                                    <?php $twoFactorEnabled = $authUser->hasTwoFactorEnabled(); ?>
                                    <a class="dropdown-item" href="<?php echo e(route('profile.show')); ?>"><i class="mdi mdi-account-circle m-r-5 text-muted"></i> Profile</a>
                                    <?php if(!$twoFactorEnabled): ?>
                                    <a class="dropdown-item" href="<?php echo e(route('2fa.setup')); ?>"><i class="mdi mdi-lock-check m-r-5 text-muted"></i> Enable Two-Factor</a>
                                    <?php else: ?>
                                    <a class="dropdown-item" href="<?php echo e(route('2fa.setup')); ?>"><i class="mdi mdi-lock-check m-r-5 text-muted"></i> Manage Two-Factor</a>
                                    <form action="<?php echo e(route('2fa.disable')); ?>" method="POST" class="px-3 py-1">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-outline-danger btn-block btn-sm">Disable 2FA</button>
                                    </form>
                                    <?php endif; ?>
                                    <div class="dropdown-divider"></div>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="px-3">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-primary btn-block">Logout</button>
                                    </form>
                                    <?php endif; ?>
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
                <?php endif; ?>

                <div class="page-content-wrapper">
                    <?php if (! empty(trim($__env->yieldContent('title')))): ?>
                        <div class="container-fluid">
                            <?php echo $__env->yieldContent('title'); ?>
                        </div>
                    <?php endif; ?>
                    <div class="container-fluid">
                        <?php echo $__env->yieldContent('content'); ?>
                    </div>
                </div>
            </div>

            <footer class="footer">
                 <?php echo e(date('Y')); ?> <?php echo e($brandName); ?>.
            </footer>
        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->
    </div>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/modernizr.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/detect.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/fastclick.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.slimscroll.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.blockUI.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/waves.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.nicescroll.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/jquery.scrollTo.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/plugins/skycons/skycons.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/plugins/raphael/raphael-min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/plugins/morris/morris.min.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/pages/dashborad.js')); ?>"></script>
    <script src="<?php echo e(asset('NewAsset/assets/js/app.js')); ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const notifDropdown = document.getElementById('notifDropdown');
            if (notifDropdown) {
                notifDropdown.addEventListener('click', function () {
                    fetch("<?php echo e(route('notifications.read')); ?>", {
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
    <?php if (! (request()->routeIs('messages.*'))): ?>
    <?php if(session('forbidden')): ?>
    <script>
        Swal.fire({ icon: 'error', title: 'Akses ditolak', text: <?php echo json_encode(session('forbidden'), 15, 512) ?>, confirmButtonText: 'OK' });
    </script>
    <?php endif; ?>
    <?php if(session('success')): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Berhasil', text: <?php echo json_encode(session('success'), 15, 512) ?>, timer: 2200, showConfirmButton: false });
    </script>
    <?php endif; ?>
    <?php if(session('error')): ?>
    <script>
        Swal.fire({ icon: 'error', title: 'Gagal', text: <?php echo json_encode(session('error'), 15, 512) ?>, confirmButtonText: 'OK' });
    </script>
    <?php endif; ?>
    <?php if(session('warning')): ?>
    <script>
        Swal.fire({ icon: 'warning', title: 'Peringatan', text: <?php echo json_encode(session('warning'), 15, 512) ?>, confirmButtonText: 'OK' });
    </script>
    <?php endif; ?>
    <?php if(session('info')): ?>
    <script>
        Swal.fire({ icon: 'info', title: 'Informasi', text: <?php echo json_encode(session('info'), 15, 512) ?>, confirmButtonText: 'OK' });
    </script>
    <?php endif; ?>
    <?php if(session('status')): ?>
    <script>
        Swal.fire({ icon: 'info', title: 'Status', text: <?php echo json_encode(session('status'), 15, 512) ?>, confirmButtonText: 'OK' });
    </script>
    <?php endif; ?>
    <?php endif; ?>
    <?php if($brandLocation && $brandLocation->custom_js_url): ?>
    <script src="<?php echo e($brandLocation->custom_js_url); ?>"></script>
    <?php endif; ?>
</body>
</html>
<?php /**PATH D:\www\kantorapp\resources\views/layouts/appnew.blade.php ENDPATH**/ ?>