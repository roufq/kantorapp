<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <?php
        $authUser = auth()->user();
        $brandLocation = $authUser && method_exists($authUser, 'location') ? $authUser->location : null;
        $brandName = $brandLocation && $brandLocation->brand_name ? $brandLocation->brand_name : 'KantorApp';
        $primaryColor = '#10b981'; // Mint green from image
    ?>
    <title><?php echo e($brandName); ?> | Digital Workplace</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.png')); ?>?v=<?php echo e(time()); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('apple-touch-icon.png')); ?>?v=<?php echo e(time()); ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.materialdesignicons.com/7.4.47/css/materialdesignicons.min.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">

    <link rel="stylesheet" href="<?php echo e(asset('kantorapp/css/app-modern.css')); ?>?v=<?php echo e(time()); ?>">
    
    <!-- Unpoly for SPA-like navigation -->
    <script src="https://cdn.jsdelivr.net/npm/unpoly@3.10.2/unpoly.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/unpoly@3.10.2/unpoly.min.css">

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <div id="wrapper">
        <?php if(auth()->guard()->check()): ?>
            <!-- Sidebar -->
            <div class="side-menu" id="sidebar-main" style="visibility: hidden;">

                <div class="logo-box d-flex align-items-center">
                    <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="Logo" class="logo-img"
                        style="width: 40px; height: 40px; margin-right: 12px; border-radius: 10px;">
                    <span class="brand-label">KantorApp</span>
                </div>

                <ul class="menu-list">
                    <li class="menu-title"
                        style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                        DASHBOARD & CHATS</li>
                    <li>
                        <a href="<?php echo e(route('dashboard')); ?>" class="<?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                            <i class="mdi mdi-home-outline"></i> <span><?php echo e(__('Dashboard')); ?></span>
                        </a>
                    </li>
                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee'])): ?>
                        <li>
                            <a href="<?php echo e(route('messages.index')); ?>"
                                class="<?php echo e(request()->routeIs('messages.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-email-outline"></i> <span><?php echo e(__('Messages')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="<?php echo e(route('tasks.index')); ?>" class="<?php echo e(request()->routeIs('tasks.*') ? 'active' : ''); ?>">
                            <i class="mdi mdi-checkbox-multiple-marked-outline"></i> <span><?php echo e(__('Tasks')); ?></span>
                        </a>
                    </li>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin'])): ?>
                        <?php
                            $pendingCreationCount = \App\Models\Task::where('requires_approval', true)
                                ->where('approval_status', 'pending')
                                ->when($authUser->hasRole('Location Admin'), function ($q) use ($authUser) {
                                    $q->where('approval_level', 'location_admin')
                                        ->whereHas('assignee', function ($qq) use ($authUser) {
                                            $qq->where('location_id', $authUser->location_id);
                                        });
                                })->count();
                        ?>
                        <li>
                            <a href="<?php echo e(route('tasks.progress.approvals')); ?>"
                                class="<?php echo e(request()->routeIs('tasks.progress.approvals') ? 'active' : ''); ?>">
                                <i class="mdi mdi-shield-check-outline"></i>
                                <span><?php echo e(__('Task Approvals')); ?> <?php if($pendingCreationCount > 0): ?><span
                                class="badge rounded-pill bg-warning text-dark ms-2"><?php echo e($pendingCreationCount); ?></span><?php endif; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('shifts.rosters.calendar')); ?>"
                                class="<?php echo e(request()->routeIs('shifts.rosters.calendar') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-blank-multiple"></i> <span><?php echo e(__('Calendar')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin'])): ?>
                        <li>
                            <a href="<?php echo e(route('employees.index')); ?>"
                                class="<?php echo e(request()->routeIs('employees.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-group-outline"></i> <span><?php echo e(__('Employees')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee'])): ?>
                        <li>
                            <a href="<?php echo e(route('location-change-requests.index')); ?>"
                                class="<?php echo e(request()->routeIs('location-change-requests.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-map-marker-distance"></i> <span><?php echo e(__('Location Change')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="menu-title"
                        style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                        TIME & ATTENDANCE</li>
                    <li>
                        <a href="<?php echo e(route('attendance.checkin')); ?>"
                            class="<?php echo e(request()->routeIs('attendance.checkin') ? 'active' : ''); ?>">
                            <i class="mdi mdi-clock-check-outline"></i> <span><?php echo e(__('Check In/Out')); ?></span>
                        </a>
                    </li>
                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin'])): ?>
                        <li>
                            <a href="<?php echo e(route('attendance.report')); ?>"
                                class="<?php echo e(request()->routeIs('attendance.report') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-search"></i> <span><?php echo e(__('Attendance Report')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('attendance.absences')); ?>"
                                class="<?php echo e(request()->routeIs('attendance.absences') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-alert-outline"></i> <span><?php echo e(__('Absences')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('attendance.recap')); ?>"
                                class="<?php echo e(request()->routeIs('attendance.recap') ? 'active' : ''); ?>">
                                <i class="mdi mdi-clipboard-text-play-outline"></i> <span><?php echo e(__('Recap')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee'])): ?>
                        <li>
                            <a href="<?php echo e(route('overtime.index')); ?>"
                                class="<?php echo e(request()->routeIs('overtime.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-progress-clock"></i> <span><?php echo e(__('Overtime')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('reports.index')); ?>"
                                class="<?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-clipboard-text-outline"></i> <span><?php echo e(__('Reports')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin'])): ?>
                        <li>
                            <a href="<?php echo e(route('work-recaps.index')); ?>"
                                class="<?php echo e(request()->routeIs('work-recaps.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-timer-sand"></i> <span><?php echo e(__('Working Hours Recap')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('work-targets.index')); ?>"
                                class="<?php echo e(request()->routeIs('work-targets.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-bullseye-arrow"></i> <span><?php echo e(__('Target Working Hours')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin'])): ?>
                        <?php
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
                        ?>
                        <li>
                            <a href="<?php echo e(route('holidays.index')); ?>"
                                class="<?php echo e(request()->routeIs('holidays.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-star-outline"></i>
                                <span><?php echo e(__('Holidays')); ?> <?php if($upcomingHolidayCount > 0): ?><span
                                class="badge rounded-pill bg-info text-dark ms-2"><?php echo e($upcomingHolidayCount); ?></span><?php endif; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('weekly-offs.index')); ?>"
                                class="<?php echo e(request()->routeIs('weekly-offs.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-x"></i> <span><?php echo e(__('Weekly Offs')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'Location Admin', 'Employee'])): ?>
                        <?php
                            if ($authUser->hasRole('Super Admin')) {
                                $pendingLeavesCount = \App\Models\EmployeeLeave::where('status', 'pending')->count();
                            } elseif ($authUser->hasRole('Location Admin')) {
                                $pendingLeavesCount = \App\Models\EmployeeLeave::where('status', 'pending')->where('location_id', $authUser->location_id)->count();
                            } else {
                                $pendingLeavesCount = 0;
                            }
                        ?>
                        <li>
                            <a href="<?php echo e(route('leaves.index')); ?>" class="<?php echo e(request()->routeIs('leaves.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-arrow-right-outline"></i>
                                <span><?php echo e(__('Leaves')); ?>

                                    <?php if($pendingLeavesCount > 0 && ($authUser->hasRole('Super Admin') || $authUser->hasRole('Location Admin'))): ?><span
                                    class="badge rounded-pill bg-warning text-dark ms-2"><?php echo e($pendingLeavesCount); ?></span><?php endif; ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasRole('Location Admin')): ?>
                        <li class="menu-title"
                            style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                            LOCATION ADMIN</li>
                        <li>
                            <a href="<?php echo e(route('location-admin-tasks.index')); ?>"
                                class="<?php echo e(request()->routeIs('location-admin-tasks.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-map-marker-radius"></i> <span><?php echo e(__('Location Tasks')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('shifts.rosters.index')); ?>"
                                class="<?php echo e(request()->routeIs('shifts.rosters.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-calendar-clock-outline"></i> <span><?php echo e(__('Weekly Rosters')); ?></span>
                            </a>
                        </li>
                        <?php if($authUser->location_id): ?>
                            <li>
                                <a href="<?php echo e(route('locations.settings', $authUser->location_id)); ?>"
                                    class="<?php echo e(request()->routeIs('locations.settings*') ? 'active' : ''); ?>">
                                    <i class="mdi mdi-office-building-cog-outline"></i>
                                    <span><?php echo e(__('My Location Settings')); ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if($authUser->hasAnyRole(['Super Admin', 'HR'])): ?>
                        <li class="menu-title"
                            style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                            TALENT & HR</li>
                        <li>
                            <a href="<?php echo e(route('jobdesks.index')); ?>"
                                class="<?php echo e(request()->routeIs('jobdesks.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-briefcase-variant-outline"></i> <span><?php echo e(__('Jobdesk & Catalog')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('jobdesk-targets.index')); ?>"
                                class="<?php echo e(request()->routeIs('jobdesk-targets.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-bullseye-arrow"></i> <span><?php echo e(__('Target Output')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('approval-rules.index')); ?>"
                                class="<?php echo e(request()->routeIs('approval-rules.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-playlist-check"></i> <span><?php echo e(__('Approval Rules')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('employee-positions.index')); ?>"
                                class="<?php echo e(request()->routeIs('employee-positions.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-tie-outline"></i> <span><?php echo e(__('Position History')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('employee-transfers.index')); ?>"
                                class="<?php echo e(request()->routeIs('employee-transfers.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-switch-outline"></i> <span><?php echo e(__('Transfers')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('employee-contracts.index')); ?>"
                                class="<?php echo e(request()->routeIs('employee-contracts.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-file-certificate-outline"></i> <span><?php echo e(__('Contracts')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if($authUser->hasRole('Super Admin')): ?>
                        <li class="menu-title"
                            style="padding: 20px 20px 10px; font-size: 0.7rem; color: #94a3b8; font-weight: 800; letter-spacing: 1px;">
                            ADMINISTRATION</li>
                        <li>
                            <a href="<?php echo e(route('users.index')); ?>" class="<?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-badge-account-horizontal-outline"></i> <span><?php echo e(__('Users')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('divisions.index')); ?>"
                                class="<?php echo e(request()->routeIs('divisions.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-layers-triple-outline"></i> <span><?php echo e(__('Divisions')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('locations.index')); ?>"
                                class="<?php echo e(request()->routeIs('locations.index') || request()->routeIs('locations.show') || request()->routeIs('locations.edit') ? 'active' : ''); ?>">
                                <i class="mdi mdi-map-search-outline"></i> <span><?php echo e(__('All Locations')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('location-shifts.index')); ?>"
                                class="<?php echo e(request()->routeIs('location-shifts.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-clock-fast"></i> <span><?php echo e(__('Location Shifts')); ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo e(route('location-admins.index')); ?>"
                                class="<?php echo e(request()->routeIs('location-admins.*') ? 'active' : ''); ?>">
                                <i class="mdi mdi-account-cog-outline"></i> <span><?php echo e(__('Location Admins')); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <script>
                    (function() {
                        const sidebar = document.getElementById('sidebar-main');
                        const savedScroll = sessionStorage.getItem('kantorapp_sidebar_scroll');
                        if (savedScroll && sidebar) {
                            sidebar.scrollTop = savedScroll;
                        }
                        sidebar.style.visibility = 'visible';
                    })();
                </script>
            </div>
        <?php endif; ?>

        <!-- Main Content Wrapper -->
        <div class="main-content">
            <?php if(auth()->guard()->check()): ?>
                <div class="mobile-header">
                    <div class="d-flex align-items-center">
                        <button class="btn p-0 me-3 text-dark sidebar-toggle" id="mobile-toggle">
                            <i class="mdi mdi-menu fs-1"></i>
                        </button>
                        <img src="<?php echo e(asset('assets/img/logo.png')); ?>" alt="Logo"
                            style="width: 32px; height: 32px; margin-right: 10px; border-radius: 8px;">
                        <span class="fw-bold fs-4">KantorApp</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="mdi mdi-bell-outline fs-4 text-muted"></i>
                        <div class="dropdown">
                            <button class="btn p-0 border-0 dropdown-toggle hide-caret" type="button" data-bs-toggle="dropdown">
                                <img src="<?php echo e($authUser->profile_photo_path ? asset('storage/' . $authUser->profile_photo_path) : asset('assets/img/user2-160x160.jpg')); ?>"
                                    class="rounded-pill shadow-sm" style="width: 32px; height: 32px; object-fit: cover;" alt="">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2 mt-2">
                                <li><a class="dropdown-item py-2 px-3 rounded-3 mb-1" href="<?php echo e(route('profile.show')); ?>"><i class="mdi mdi-account-outline me-2"></i>Profile</a></li>
                                <li><hr class="dropdown-divider mx-2"></li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="dropdown-item py-2 px-3 rounded-3 text-danger fw-bold">
                                            <i class="mdi mdi-logout me-2"></i>Sign Out
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Top Navigation -->
                <div class="top-nav">
                    <div class="greeting">
                        <h1>Good Morning, <?php echo e(explode(' ', $authUser->name)[0]); ?>!</h1>
                        <p><?php echo e(now()->format('D, M d, Y, h:i A')); ?></p>
                    </div>

                    <div class="top-actions">
                        <div class="search-pill">
                            <i class="mdi mdi-magnify"></i>
                            <input type="text" placeholder="Search">
                        </div>

                        <?php
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
                        ?>
                        <div class="dropdown">
                            <div class="nav-icon-btn dropdown-toggle hide-caret" id="notifDropdown" data-toggle="dropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="mdi mdi-bell-outline"></i>
                                <?php if($unreadNotificationCount > 0): ?>
                                    <div class="badge-dot"></div>
                                <?php endif; ?>
                            </div>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="notifDropdown"
                                style="border-radius: 20px; min-width: 320px; padding: 15px; z-index: 1060; margin-top: 20px !important;">
                                <li class="px-3 py-2 border-bottom mb-2 d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold"><?php echo e(__('Notifications')); ?></h6>
                                    <span class="badge bg-soft-success text-success"><?php echo e($unreadNotificationCount); ?>

                                        New</span>
                                </li>
                                <?php if($recentTaskNotifs->count() > 0): ?>
                                    <?php $__currentLoopData = $recentTaskNotifs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $data = $notif->data ?? [];
                                            $title = $data['task_title'] ?? 'Task Update';
                                            $status = $data['status'] ?? 'info';
                                        ?>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 rounded-4 mb-1" href="<?php echo e(route('tasks.index')); ?>">
                                                <div class="d-flex align-items-center">
                                                    <div class="activity-icon bg-soft-info text-info me-3"
                                                        style="width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;">
                                                        <i class="mdi mdi-calendar-check fs-6"></i>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <p class="mb-0 fw-bold text-truncate" style="font-size: 0.85rem;">
                                                            <?php echo e($title); ?></p>
                                                        <small class="text-muted"
                                                            style="font-size: 0.75rem;"><?php echo e($notif->created_at->diffForHumans()); ?></small>
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <li class="text-center py-4 text-muted">
                                        <i class="mdi mdi-bell-off-outline fs-1 opacity-25"></i>
                                        <p class="mt-2 small fw-medium">No new notifications</p>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider mx-2 opacity-10">
                                </li>
                                <li>
                                    <a class="dropdown-item text-center fw-bold py-2 text-primary"
                                        href="<?php echo e(route('tasks.index')); ?>">
                                        <?php echo e(__('View All Notifications')); ?>

                                    </a>
                                </li>
                            </ul>
                        </div>

                        <?php
                            $avatarUrl = $authUser->profile_photo_path ? asset('storage/' . $authUser->profile_photo_path) : asset('assets/img/user2-160x160.jpg');
                        ?>
                        <div class="dropdown">
                            <button class="btn p-0 border-0 dropdown-toggle hide-caret shadow-none" type="button" id="userDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?php echo e($avatarUrl); ?>" class="user-profile-img shadow-sm" alt="Profile" style="cursor: pointer; object-fit: cover;">
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userDropdown"
                                style="border-radius: 20px; min-width: 260px; padding: 15px; z-index: 1060; margin-top: 20px !important;">
                                <li class="px-3 py-3 border-bottom mb-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-0 fw-bold text-truncate"
                                                style="font-size: 1rem; max-width: 150px;"><?php echo e($authUser->name); ?></h6>
                                            <small class="text-muted fw-semibold"
                                                style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;"><?php echo e($authUser->getRoleNames()->first()); ?></small>
                                        </div>
                                        <div class="badge bg-soft-success text-success rounded-pill px-2 py-1"
                                            style="font-size: 0.7rem;">Active</div>
                                    </div>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2 px-3 rounded-pill fw-medium mb-1 mt-1"
                                        href="<?php echo e(route('profile.show')); ?>">
                                        <i class="mdi mdi-account-circle-outline me-2 fs-5 opacity-75"></i>
                                        <?php echo e(__('My Profile')); ?>

                                    </a>
                                </li>
                                <?php $twoFactorEnabled = $authUser->hasTwoFactorEnabled(); ?>
                                <li>
                                    <a class="dropdown-item py-2 px-3 rounded-pill fw-medium mb-1"
                                        href="<?php echo e(route('2fa.setup')); ?>">
                                        <i class="mdi mdi-shield-link-variant-outline me-2 fs-5 opacity-75"></i>
                                        <?php echo e($twoFactorEnabled ? __('Security Settings') : __('Enable 2FA')); ?>

                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider mx-2 opacity-10">
                                </li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit"
                                            class="dropdown-item py-2 px-3 rounded-pill fw-bold text-danger">
                                            <i class="mdi mdi-power-cycle me-2 fs-5 opacity-75"></i> <?php echo e(__('Sign Out')); ?>

                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="page-body">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="<?php echo e(asset('kantorapp/js/app-modern.js')); ?>?v=<?php echo e(time()); ?>"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html><?php /**PATH D:\www\kantorapp\resources\views/layouts/appnew.blade.php ENDPATH**/ ?>