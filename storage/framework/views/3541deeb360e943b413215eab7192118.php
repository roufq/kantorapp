<!doctype html>
<html lang="id">
<!--begin::Head-->

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Office App | Dashboard v2</title>
  <!--begin::Accessibility Meta Tags-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
  <meta name="color-scheme" content="light dark" />
  <meta name="theme-color" content="#007bff" media="(prefers-color-scheme: light)" />
  <meta name="theme-color" content="#1a1a1a" media="(prefers-color-scheme: dark)" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
  <!--end::Accessibility Meta Tags-->
  <!--begin::Primary Meta Tags-->
  <meta name="title" content="AdminLTE | Dashboard v2" />
  <meta name="author" content="ColorlibHQ" />
  <meta
    name="description"
    content="AdminLTE is a Free Bootstrap 5 Admin Dashboard, 30 example pages using Vanilla JS. Fully accessible with WCAG 2.1 AA compliance." />
  <meta
    name="keywords"
    content="bootstrap 5, bootstrap, bootstrap 5 admin dashboard, bootstrap 5 dashboard, bootstrap 5 charts, bootstrap 5 calendar, bootstrap 5 datepicker, bootstrap 5 tables, bootstrap 5 datatable, vanilla js datatable, colorlibhq, colorlibhq dashboard, colorlibhq admin dashboard, accessible admin panel, WCAG compliant" />
  <!--end::Primary Meta Tags-->
  <!--begin::Accessibility Features-->
  <!-- Skip links will be dynamically added by accessibility.js -->
  <meta name="supported-color-schemes" content="light dark" />
  <link rel="preload" href="<?php echo e(asset('css/adminlte.css')); ?>" as="style" />
  <!--end::Accessibility Features-->
  <!--begin::Fonts-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
    integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
    crossorigin="anonymous"
    media="print"
    onload="this.media='all'" />
  <!--end::Fonts-->
  <!-- Branding Hook: Inject per-location brand styles and assets -->
<?php
  $brandLocation = auth()->check() ? auth()->user()->location : null;
  $brandName = $brandLocation && $brandLocation->brand_name ? $brandLocation->brand_name : null;
  $brandLogoUrl = $brandLocation && $brandLocation->brand_logo_url ? (\Illuminate\Support\Str::startsWith($brandLocation->brand_logo_url, ['http://','https://']) ? $brandLocation->brand_logo_url : asset($brandLocation->brand_logo_url)) : null;
  $primaryColor = $brandLocation && $brandLocation->primary_color ? $brandLocation->primary_color : null;
  $secondaryColor = $brandLocation && $brandLocation->secondary_color ? $brandLocation->secondary_color : null;
  $brandName = $brandName ?: 'CfgBrand';
?>
  <?php if($primaryColor || $secondaryColor): ?>
  <style>
    :root {
      <?php if($primaryColor): ?> --brand-primary: <?php echo e($primaryColor); ?>; <?php endif; ?>
      <?php if($secondaryColor): ?> --brand-secondary: <?php echo e($secondaryColor); ?>; <?php endif; ?>
    }
    .brand-text { color: var(--brand-primary, inherit); }
    .btn-primary { background-color: var(--brand-primary, #0d6efd); border-color: var(--brand-primary, #0d6efd); }
    .text-primary { color: var(--brand-primary, #0d6efd) !important; }
    .bg-primary { background-color: var(--brand-primary, #0d6efd) !important; }
  </style>
  <?php endif; ?>
  <link rel="stylesheet" href="/css/custom-cfg.css">
  <link rel="stylesheet" href="/themes/green.css">
  <?php if($brandLocation && $brandLocation->custom_css_url): ?>
  <link rel="stylesheet" href="<?php echo e($brandLocation->custom_css_url); ?>" />
  <?php endif; ?>
  <?php
  $themeName = $brandLocation ? ($brandLocation->getSetting('theme', null)) : null;
  ?>
  <?php if($themeName): ?>
  <link rel="stylesheet" href="<?php echo e(asset('themes/' . $themeName . '.css')); ?>" />
  <?php endif; ?>
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(OverlayScrollbars)-->
  <!--begin::Third Party Plugin(Bootstrap Icons)-->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    crossorigin="anonymous" />
  <!--end::Third Party Plugin(Bootstrap Icons)-->
  <!--begin::Required Plugin(AdminLTE)-->
  <link rel="stylesheet" href="<?php echo e(asset('css/adminlte.css')); ?>" />
  <!--end::Required Plugin(AdminLTE)-->
  <!--begin::Legacy Badge Compatibility (BS4 -> BS5)-->
  <style>
    /* Ensure old Bootstrap 4 badge classes render correctly on BS5 */
    .badge.badge-primary {
      background-color: var(--bs-primary);
      color: #fff;
    }

    .badge.badge-secondary {
      background-color: var(--bs-secondary);
      color: #fff;
    }

    .badge.badge-success {
      background-color: var(--bs-success);
      color: #fff;
    }

    .badge.badge-danger {
      background-color: var(--bs-danger);
      color: #fff;
    }

    .badge.badge-warning {
      background-color: var(--bs-warning);
      color: #212529;
    }

    .badge.badge-info {
      background-color: var(--bs-info);
      color: #fff;
    }

    .badge.badge-light {
      background-color: var(--bs-light);
      color: #212529;
    }

    .badge.badge-dark {
      background-color: var(--bs-dark);
      color: #fff;
    }

    /* Table helpers */
    .table .code-badge {
      font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    .table-actions .dropdown-toggle::after {
      display: none;
    }
  </style>
  <!--end::Legacy Badge Compatibility (BS4 -> BS5)-->
  <!-- apexcharts -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
    integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0="
    crossorigin="anonymous" />
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
  <!--begin::App Wrapper-->
  <div class="app-wrapper">
    <!--begin::Header-->
    <nav class="app-header navbar navbar-expand bg-body position-relative" style="z-index:2002">
      <!--begin::Container-->
      <div class="container-fluid">
        <!--begin::Start Navbar Links-->
        <ul class="navbar-nav">
          <li class="nav-item">
            <button id="headerHamburger" class="nav-link btn btn-link p-0 position-relative" data-lte-toggle="sidebar" type="button" role="button" aria-label="Toggle sidebar" style="z-index:2001">
              <i class="bi bi-list"></i>
            </button>
          </li>
          <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Home</a></li>
          <li class="nav-item d-none d-md-block"><a href="#" class="nav-link">Contact</a></li>
        </ul>
        <!--end::Start Navbar Links-->
        <!--begin::End Navbar Links-->
        <ul class="navbar-nav ms-auto">
          <!--begin::Navbar Search-->
          <li class="nav-item">
            <a class="nav-link" data-widget="navbar-search" href="#" role="button">
              <i class="bi bi-search"></i>
            </a>
          </li>
          <!--end::Navbar Search-->
          <!--begin::Messages Dropdown Menu-->
          <?php
          $authUser = auth()->user();
          $unreadMessagesCount = \App\Models\Message::where('receiver_id', $authUser->id)->whereNull('read_at')->count();
          ?>
          <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
              <i class="bi bi-chat-text"></i>
              <?php if($unreadMessagesCount > 0): ?>
              <span class="navbar-badge badge text-bg-danger"><?php echo e($unreadMessagesCount); ?></span>
              <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <span class="dropdown-item dropdown-header"><?php echo e($unreadMessagesCount); ?> Pesan belum dibaca</span>
              <div class="dropdown-divider"></div>
              <a href="<?php echo e(route('messages.index')); ?>" class="dropdown-item">
                <i class="bi bi-inbox-fill me-2"></i> Pergi ke pesan
              </a>
            </div>
          </li>
          <!--end::Messages Dropdown Menu-->
          <!--begin::Notifications Dropdown Menu-->
          <?php
          $pendingOvertimeCount = \App\Models\OvertimeApproval::where('master_id', $authUser->id)->where('status', 'pending')->count();
          $pendingReportCount = 0;
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
          $totalNotifications = $unreadMessagesCount + $pendingOvertimeCount + $pendingReportCount;
          ?>
          <li class="nav-item dropdown">
            <a class="nav-link" data-bs-toggle="dropdown" href="#">
              <i class="bi bi-bell-fill"></i>
              <?php if($totalNotifications > 0): ?>
              <span class="navbar-badge badge text-bg-warning"><?php echo e($totalNotifications); ?></span>
              <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <span class="dropdown-item dropdown-header"><?php echo e($totalNotifications); ?> Notifikasi</span>
              <div class="dropdown-divider"></div>
              <a href="<?php echo e(route('messages.index')); ?>" class="dropdown-item">
                <i class="bi bi-envelope me-2"></i> <?php echo e($unreadMessagesCount); ?> pesan baru
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?php echo e(route('reports.index')); ?>" class="dropdown-item">
                <i class="bi bi-clipboard-check me-2"></i> <?php echo e($pendingReportCount); ?> laporan menunggu
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?php echo e(route('overtime.index')); ?>" class="dropdown-item">
                <i class="bi bi-clock me-2"></i> <?php echo e($pendingOvertimeCount); ?> overtime menunggu
              </a>
              <div class="dropdown-divider"></div>
              <a href="<?php echo e(route('dashboard')); ?>" class="dropdown-item dropdown-footer">Lihat semua</a>
            </div>
          </li>
          <!--end::Notifications Dropdown Menu-->
          <!--begin::Fullscreen Toggle-->
          <li class="nav-item">
            <a class="nav-link" href="#" data-lte-toggle="fullscreen">
              <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
              <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none"></i>
            </a>
          </li>
          <!--end::Fullscreen Toggle-->
          <!--begin::User Menu Dropdown-->
          <?php
          $avatarPath = auth()->user()->profile_photo_path;
          $avatarUrl = asset('assets/img/user2-160x160.jpg');
          if ($avatarPath) {
          $normalized = str_replace('\\','/',$avatarPath);
          if (\Illuminate\Support\Facades\Storage::disk('public')->exists($normalized)) {
          $avatarUrl = asset('storage/' . ltrim($normalized, '/'));
          }
          }
          ?>
          <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
              <img
                src="<?php echo e($avatarUrl); ?>"
                class="user-image rounded-circle shadow"
                alt="User Image" />
              <span class="d-none d-md-inline"><?php echo e(auth()->user()->name); ?> - <?php echo e(auth()->user()->getRoleNames()->first()); ?></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
              <!--begin::User Image-->
              <li class="user-header text-bg-primary text-center">
                <img
                  src="<?php echo e($avatarUrl); ?>"
                  class="rounded-circle shadow mb-2"
                  alt="User Image" />
                <p class="mb-0"><?php echo e(auth()->user()->name); ?> - <?php echo e(auth()->user()->getRoleNames()->first()); ?></p>
                <small>Member since <?php echo e(auth()->user()->created_at->format('M. Y')); ?></small>
              </li>
              <!--end::User Image-->
              <!--begin::Menu Body-->
              <li class="user-body">
                <?php $twoFactorEnabled = auth()->user()->hasTwoFactorEnabled(); ?>
                <?php if(!$twoFactorEnabled): ?>
                <a href="<?php echo e(route('2fa.setup')); ?>" class="btn btn-outline-primary btn-sm w-100 mb-2" aria-label="Enable two-factor authentication">
                  Enable Two-Factor Authentication
                </a>
                <?php else: ?>
                <a href="<?php echo e(route('2fa.setup')); ?>" class="btn btn-outline-primary btn-sm w-100 mb-2" aria-label="Manage two-factor authentication">
                  Manage Two-Factor Authentication
                </a>
                <form action="<?php echo e(route('2fa.disable')); ?>" method="POST" class="d-grid">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-outline-danger btn-sm" aria-label="Disable two-factor authentication">
                    Disable Two-Factor
                  </button>
                </form>
                <?php endif; ?>
              </li>
              <!--end::Menu Body-->
              <!--begin::Menu Footer-->
              <li class="user-footer d-flex justify-content-between">
                <a href="<?php echo e(route('profile.show')); ?>" class="btn btn-default btn-flat">Profile</a>
                <form method="POST" action="<?php echo e(route('logout')); ?>" style="display: inline;">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-default btn-flat float-end">Sign out</button>
                </form>
              </li>
              <!--end::Menu Footer-->
            </ul>
          </li>
          <!--end::User Menu Dropdown-->
        </ul>
        <!--end::End Navbar Links-->
      </div>
      <!--end::Container-->
    </nav>
    <!--end::Header-->
    <!--begin::Sidebar-->
    <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
      <!--begin::Sidebar Brand-->
      <div class="sidebar-brand position-relative">
        <!--begin::Brand Link-->
        <a href="<?php echo e(url('/')); ?>" class="brand-link">
          <!--begin::Brand Image-->
          <img
            src="<?php echo e($brandLogoUrl ?: asset('assets/img/AdminLTELogo.png')); ?>"
            alt="Logo"
            class="brand-image opacity-75 shadow" />
          <!--end::Brand Image-->
          <!--begin::Brand Text-->
          <span class="brand-text fw-light"><?php echo e($brandName ?: 'Office App'); ?></span>
          <!--end::Brand Text-->
          </button>
          <!--begin::Sidebar Mobile Toggle (Hamburger on brand row)-->
          <button
            id="sidebarHamburger"
            class="btn btn-link p-0 d-lg-none position-absolute end-0 top-50 translate-middle-y me-2"
            type="button"
            aria-label="Toggle sidebar"
            aria-controls="navigation"
            style="color: inherit; z-index: 2000; pointer-events: auto;">
            <i class="bi bi-list fs-4"></i>
          </button>
          <!--end::Sidebar Mobile Toggle (Hamburger on brand row)-->
      </div>
      <!--end::Sidebar Brand-->
      <!--begin::Sidebar Wrapper-->
      <div class="sidebar-wrapper">
        <nav class="mt-2">
          <!--begin::Sidebar Menu-->
          <ul
            class="nav sidebar-menu flex-column"
            data-lte-toggle="treeview"
            role="navigation"
            aria-label="Main navigation"
            data-accordion="false"
            id="navigation">
            <li class="nav-header">Main</li>
            <li class="nav-item">
              <a href="<?php echo e(route('dashboard')); ?>" class="nav-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-speedometer"></i>
                <p>Dashboard</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('messages.index')); ?>" class="nav-link <?php echo e(request()->routeIs('messages.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-chat-dots"></i>
                <p>Messages</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('tasks.index')); ?>" class="nav-link <?php echo e(request()->routeIs('tasks.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-check-circle"></i>
                <p>Tasks</p>
              </a>
            </li>
            <?php if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
            <li class="nav-item">
              <?php
                $pendingCreationCount = \App\Models\Task::where('requires_approval', true)
                  ->where('approval_status', 'pending')
                  ->when(auth()->user()->hasRole('Admin Lokasi'), function ($q) {
                      $q->where('approval_level', 'location_admin')
                        ->whereHas('assignee', function ($qq) {
                            $qq->where('location_id', auth()->user()->location_id);
                        });
                  })
                  ->count();
              ?>
              <a href="<?php echo e(route('tasks.progress.approvals')); ?>" class="nav-link <?php echo e(request()->routeIs('tasks.progress.approvals') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-shield-check"></i>
                <p>Approval Tugas <?php if($pendingCreationCount>0): ?><span class="badge text-bg-warning ms-2"><?php echo e($pendingCreationCount); ?></span><?php endif; ?></p>
              </a>
            </li>
            <?php endif; ?>
            <?php if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('shifts.rosters.calendar')); ?>" class="nav-link <?php echo e(request()->routeIs('shifts.rosters.calendar') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-calendar-week"></i>
                <p>Kalender</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('karyawans.index')); ?>" class="nav-link <?php echo e(request()->routeIs('karyawans.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-person-badge"></i>
                <p>Karyawan</p>
              </a>
            </li>
            <?php endif; ?>
            <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi') || auth()->user()->hasRole('Karyawan')): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('location-change-requests.index')); ?>" class="nav-link <?php echo e(request()->routeIs('location-change-requests.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-arrow-left-right"></i>
                <p>Location Change</p>
              </a>
            </li>
            <?php endif; ?>
            <li class="nav-item <?php echo e(request()->routeIs('attendance.*') ? 'menu-open' : ''); ?>">
              <a href="#" class="nav-link">
                <i class="nav-icon bi bi-calendar-check"></i>
                <p>Attendance</p>
                <i class="nav-arrow bi bi-chevron-right"></i>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo e(route('attendance.checkin')); ?>" class="nav-link <?php echo e(request()->routeIs('attendance.checkin') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Check In/Out</p>
                  </a>
                </li>
                <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('attendance.report')); ?>" class="nav-link <?php echo e(request()->routeIs('attendance.report') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Report</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo e(route('attendance.absences')); ?>" class="nav-link <?php echo e(request()->routeIs('attendance.absences') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Absences</p>
                  </a>
                </li>
                <li class="nav-item">
                  <a href="<?php echo e(route('attendance.recap')); ?>" class="nav-link <?php echo e(request()->routeIs('attendance.recap') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Recap</p>
                  </a>
                </li>
                <?php endif; ?>

              </ul>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('overtime.index')); ?>" class="nav-link <?php echo e(request()->routeIs('overtime.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-clock"></i>
                <p>Overtime</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('reports.index')); ?>" class="nav-link <?php echo e(request()->routeIs('reports.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-clipboard-check"></i>
                <p>Laporan</p>
              </a>
            </li>
            <?php if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('work-recaps.index')); ?>" class="nav-link <?php echo e(request()->routeIs('work-recaps.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-clock-history"></i>
                <p>Rekap Jam Kerja</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('work-targets.index')); ?>" class="nav-link <?php echo e(request()->routeIs('work-targets.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-bullseye"></i>
                <p>Target Jam Kerja</p>
              </a>
            </li>
            <?php endif; ?>

            <?php if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi')): ?>
            <li class="nav-item">
              <?php
              $today = now()->toDateString();
              if (auth()->user()->hasRole('Super Admin')) {
              $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date','>=',$today)->count();
              } else {
              $locId = auth()->user()->location_id;
              $upcomingHolidayCount = \App\Models\Holiday::active()->whereDate('date','>=',$today)
              ->where(function($q) use ($locId){
              $q->where('is_national',true)
              ->orWhere(function($qq) use ($locId){ $qq->where('is_national',false)->where('location_id',$locId); });
              })->count();
              }
              ?>
              <a href="<?php echo e(route('holidays.index')); ?>" class="nav-link <?php echo e(request()->routeIs('holidays.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-flag"></i>
                <p>Holidays <?php if($upcomingHolidayCount>0): ?><span class="badge text-bg-info ms-2"><?php echo e($upcomingHolidayCount); ?></span><?php endif; ?></p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('weekly-offs.index')); ?>" class="nav-link <?php echo e(request()->routeIs('weekly-offs.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-calendar-x"></i>
                <p>Weekly Offs</p>
              </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
              <?php
              if (auth()->user()->hasRole('Super Admin')) {
              $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->count();
              } elseif (auth()->user()->hasRole('Admin Lokasi')) {
              $pendingLeavesCount = \App\Models\EmployeeLeave::where('status','pending')->where('location_id', auth()->user()->location_id)->count();
              } else {
              $pendingLeavesCount = 0;
              }
              ?>
              <a href="<?php echo e(route('leaves.index')); ?>" class="nav-link <?php echo e(request()->routeIs('leaves.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-person-exclamation"></i>
                <p>Leaves <?php if($pendingLeavesCount>0 && (auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin Lokasi'))): ?><span class="badge text-bg-warning ms-2"><?php echo e($pendingLeavesCount); ?></span><?php endif; ?></p>
              </a>
            </li>

            <?php if(auth()->user()->hasRole('Admin Lokasi')): ?>
            <li class="nav-header">Location</li>
            <li class="nav-item">
              <a href="<?php echo e(route('location-admin-tasks.index')); ?>" class="nav-link <?php echo e(request()->routeIs('location-admin-tasks.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-geo"></i>
                <p>Location Tasks</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="nav-link <?php echo e(request()->routeIs('shifts.rosters.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-dot"></i>
                <p>Weekly Rosters</p>
              </a>
            </li>
            <?php if(auth()->user()->location_id): ?>
            <li class="nav-item">
              <a href="<?php echo e(route('locations.settings', auth()->user()->location_id)); ?>" class="nav-link <?php echo e(request()->routeIs('locations.settings*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-gear"></i>
                <p>My Location Settings</p>
              </a>
            </li>
            <?php endif; ?>
            <?php endif; ?>
            <?php if(auth()->user()->hasRole('Super Admin')): ?>
            <li class="nav-header">Administration</li>
            <li class="nav-item">
              <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(request()->routeIs('users.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-people"></i>
                <p>Users</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('divisions.index')); ?>" class="nav-link <?php echo e(request()->routeIs('divisions.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-diagram-3"></i>
                <p>Divisions</p>
              </a>
            </li>
            <li class="nav-item <?php echo e(request()->routeIs('locations.*') || request()->routeIs('shifts.scheduler') || request()->routeIs('shifts.rosters.*') || request()->routeIs('location-shifts.*') ? 'menu-open' : ''); ?>">
              <a href="#" class="nav-link">
                <i class="nav-icon bi bi-geo-alt"></i>
                <p>Locations</p>
                <i class="nav-arrow bi bi-chevron-right"></i>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item">
                  <a href="<?php echo e(route('locations.index')); ?>" class="nav-link <?php echo e(request()->routeIs('locations.index') || request()->routeIs('locations.show') || request()->routeIs('locations.edit') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>All Locations</p>
                  </a>
                </li>
                <?php if(auth()->user()->hasAnyRole(['Super Admin','Admin Lokasi'])): ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('shifts.rosters.index')); ?>" class="nav-link <?php echo e(request()->routeIs('shifts.rosters.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Weekly Rosters</p>
                  </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                  <a href="<?php echo e(route('location-shifts.index')); ?>" class="nav-link <?php echo e(request()->routeIs('location-shifts.*') ? 'active' : ''); ?>">
                    <i class="nav-icon bi bi-dot"></i>
                    <p>Location Shifts</p>
                  </a>
                </li>
              </ul>
            </li>
            <li class="nav-item">
              <a href="<?php echo e(route('location-admins.index')); ?>" class="nav-link <?php echo e(request()->routeIs('location-admins.*') ? 'active' : ''); ?>">
                <i class="nav-icon bi bi-person-gear"></i>
                <p>Location Admins</p>
              </a>
            </li>
            <?php endif; ?>
          </ul>
          <!--end::Sidebar Menu-->
        </nav>
      </div>
      <!--end::Sidebar Wrapper-->
    </aside>
    <!--end::Sidebar-->
    <!--begin::App Main-->
    <main class="app-main">
      <!--begin::App Content Header-->
      <div class="app-content-header">
        <!--begin::Container-->
        <?php echo $__env->yieldContent('title'); ?>
        <!--end::Container-->
      </div>
      <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
          <?php echo $__env->yieldContent('content'); ?>
        </div>
        <!--end::Container-->
      </div>
      <!--end::App Content-->
    </main>
    <!--end::App Main-->
    <!--begin::Footer-->
    <footer class="app-footer">
      <!--begin::To the end-->
      <div class="float-end d-none d-sm-inline">Anything you want</div>
      <!--end::To the end-->
      <!--begin::Copyright-->
      <strong>
        copyright&copy; 2025&nbsp;
        <a href="https://github.com/roufq" class="text-decoration-none">Roufq</a>.
      </strong>
      All rights reserved.
      <!--end::Copyright-->
    </footer>
    <!--end::Footer-->
  </div>
  <!--end::App Wrapper-->
  <!--begin::Script-->
  <!--begin::Third Party Plugin(OverlayScrollbars)-->
  <script
    src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
    crossorigin="anonymous"></script>
  <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Required Plugin(popperjs for Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(popperjs for Bootstrap 5)--><!--begin::Required Plugin(Bootstrap 5)-->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
    crossorigin="anonymous"></script>
  <!--end::Required Plugin(Bootstrap 5)--><!--begin::Required Plugin(AdminLTE)-->
  <script src="./js/adminlte.js"></script>
  <!--end::Required Plugin(AdminLTE)--><!--begin::OverlayScrollbars Configure-->
  <script>
    const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
    const Default = {
      scrollbarTheme: 'os-theme-light',
      scrollbarAutoHide: 'leave',
      scrollbarClickScroll: true,
    };
    document.addEventListener('DOMContentLoaded', function() {
      const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
      if (sidebarWrapper && OverlayScrollbarsGlobal?.OverlayScrollbars !== undefined) {
        OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
          scrollbars: {
            theme: Default.scrollbarTheme,
            autoHide: Default.scrollbarAutoHide,
            clickScroll: Default.scrollbarClickScroll,
          },
        });
      }
    });
  </script>
  <!--end::OverlayScrollbars Configure-->
  <!--begin::Mobile Sidebar Behavior-->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const isMobile = () => window.matchMedia('(max-width: 991.98px)').matches;

      const syncSidebarState = () => {
        if (isMobile()) {
          document.body.classList.remove('sidebar-open');
          document.body.classList.add('sidebar-collapse');
        } else {
          document.body.classList.add('sidebar-open');
          document.body.classList.remove('sidebar-collapse');
        }
      };

      const ensureOverlay = () => {
        let ov = document.querySelector('.sidebar-overlay');
        if (!ov) {
          ov = document.createElement('div');
          ov.className = 'sidebar-overlay';
          ov.style.position = 'fixed';
          ov.style.inset = '0';
          ov.style.background = 'rgba(0,0,0,.2)';
          ov.style.zIndex = '1030';
          ov.addEventListener('click', () => closeSidebar(), {
            passive: true
          });
          document.body.appendChild(ov);
        }
        return ov;
      };

      const removeOverlay = () => {
        const ov = document.querySelector('.sidebar-overlay');
        if (ov) ov.remove();
      };

      const openSidebar = () => {
        document.body.classList.add('sidebar-open');
        document.body.classList.remove('sidebar-collapse');
        if (isMobile()) ensureOverlay();
      };

      const closeSidebar = () => {
        document.body.classList.remove('sidebar-open');
        document.body.classList.add('sidebar-collapse');
        removeOverlay();
      };

      const closeSidebarOnMobile = () => {
        if (isMobile()) closeSidebar();
      };

      // Initial state per device
      syncSidebarState();

      // Sync on resize (debounced)
      let __rsz;
      window.addEventListener('resize', () => {
        clearTimeout(__rsz);
        __rsz = setTimeout(syncSidebarState, 150);
      });

      // Close sidebar after clicking any leaf link (including submenu items) on mobile
      document.querySelectorAll('.nav-treeview a.nav-link').forEach((link) => {
        link.addEventListener('click', closeSidebarOnMobile);
      });
      document.querySelectorAll('.sidebar-menu > .nav-item > a.nav-link').forEach((link) => {
        const href = link.getAttribute('href');
        if (href && href !== '#') {
          link.addEventListener('click', closeSidebarOnMobile);
        }
      });

      // Robust manual toggle for the mobile hamburger (works even if data-lte-toggle is ignored)
      const hamburger = document.getElementById('sidebarHamburger');
      if (hamburger) {
        hamburger.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          const open = document.body.classList.contains('sidebar-open');
          if (open) {
            closeSidebar();
          } else {
            openSidebar();
          }
        }, {
          passive: false
        });
      }

      // Also hook any element with data-lte-toggle="sidebar" to our toggle (and prevent '#')
      document.querySelectorAll('[data-lte-toggle="sidebar"]').forEach((el) => {
        ['click', 'pointerdown', 'touchstart'].forEach((evt) => {
          el.addEventListener(evt, (e) => {
            if (el.tagName.toLowerCase() === 'a') e.preventDefault();
            e.stopPropagation();
            const open = document.body.classList.contains('sidebar-open');
            if (open) {
              closeSidebar();
            } else {
              openSidebar();
            }
          }, {
            passive: false
          });
        });
      });

      // Delegation fallback so toggles work regardless of DOM state
      const delegated = (evt) => {
        const t = evt.target.closest('#headerHamburger, #sidebarHamburger, [data-lte-toggle="sidebar"]');
        if (!t) return;
        evt.preventDefault();
        evt.stopPropagation();
        const open = document.body.classList.contains('sidebar-open');
        if (open) {
          closeSidebar();
        } else {
          openSidebar();
        }
      };
      document.addEventListener('click', delegated, true);
      document.addEventListener('touchstart', delegated, true);

      // Prevent URL hash (#) when clicking anchors used as toggles
      document.addEventListener('click', function(e) {
        const a = e.target.closest('a[href="#"]');
        if (a) {
          e.preventDefault();
        }
      });
    });
  </script>
  <!--end::Mobile Sidebar Behavior-->
  <!-- OPTIONAL SCRIPTS -->
  <!-- apexcharts -->
  <script
    src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
    integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8="
    crossorigin="anonymous"></script>
  <script>
    // NOTICE!! DO NOT USE ANY OF THIS JAVASCRIPT
    // IT'S ALL JUST JUNK FOR DEMO
    // ++++++++++++++++++++++++++++++++++++++++++

    /* apexcharts
     * -------
     * Here we will create a few charts using apexcharts
     */

    //-----------------------
    // - MONTHLY SALES CHART -
    //-----------------------

    const sales_chart_options = {
      series: [{
          name: 'Digital Goods',
          data: [28, 48, 40, 19, 86, 27, 90],
        },
        {
          name: 'Electronics',
          data: [65, 59, 80, 81, 56, 55, 40],
        },
      ],
      chart: {
        height: 180,
        type: 'area',
        toolbar: {
          show: false,
        },
      },
      legend: {
        show: false,
      },
      colors: ['#0d6efd', '#20c997'],
      dataLabels: {
        enabled: false,
      },
      stroke: {
        curve: 'smooth',
      },
      xaxis: {
        type: 'datetime',
        categories: [
          '2023-01-01',
          '2023-02-01',
          '2023-03-01',
          '2023-04-01',
          '2023-05-01',
          '2023-06-01',
          '2023-07-01',
        ],
      },
      tooltip: {
        x: {
          format: 'MMMM yyyy',
        },
      },
    };

    const sales_chart = new ApexCharts(
      document.querySelector('#sales-chart'),
      sales_chart_options,
    );
    sales_chart.render();

    //---------------------------
    // - END MONTHLY SALES CHART -
    //---------------------------

    function createSparklineChart(selector, data) {
      const options = {
        series: [{
          data
        }],
        chart: {
          type: 'line',
          width: 150,
          height: 30,
          sparkline: {
            enabled: true,
          },
        },
        colors: ['var(--bs-primary)'],
        stroke: {
          width: 2,
        },
        tooltip: {
          fixed: {
            enabled: false,
          },
          x: {
            show: false,
          },
          y: {
            title: {
              formatter() {
                return '';
              },
            },
          },
          marker: {
            show: false,
          },
        },
      };

      const chart = new ApexCharts(document.querySelector(selector), options);
      chart.render();
    }

    const table_sparkline_1_data = [25, 66, 41, 89, 63, 25, 44, 12, 36, 9, 54];
    const table_sparkline_2_data = [12, 56, 21, 39, 73, 45, 64, 52, 36, 59, 44];
    const table_sparkline_3_data = [15, 46, 21, 59, 33, 15, 34, 42, 56, 19, 64];
    const table_sparkline_4_data = [30, 56, 31, 69, 43, 35, 24, 32, 46, 29, 64];
    const table_sparkline_5_data = [20, 76, 51, 79, 53, 35, 54, 22, 36, 49, 64];
    const table_sparkline_6_data = [5, 36, 11, 69, 23, 15, 14, 42, 26, 19, 44];
    const table_sparkline_7_data = [12, 56, 21, 39, 73, 45, 64, 52, 36, 59, 74];

    createSparklineChart('#table-sparkline-1', table_sparkline_1_data);
    createSparklineChart('#table-sparkline-2', table_sparkline_2_data);
    createSparklineChart('#table-sparkline-3', table_sparkline_3_data);
    createSparklineChart('#table-sparkline-4', table_sparkline_4_data);
    createSparklineChart('#table-sparkline-5', table_sparkline_5_data);
    createSparklineChart('#table-sparkline-6', table_sparkline_6_data);
    createSparklineChart('#table-sparkline-7', table_sparkline_7_data);

    //-------------
    // - PIE CHART -
    //-------------

    const pie_chart_options = {
      series: [700, 500, 400, 600, 300, 100],
      chart: {
        type: 'donut',
      },
      labels: ['Chrome', 'Edge', 'FireFox', 'Safari', 'Opera', 'IE'],
      dataLabels: {
        enabled: false,
      },
      colors: ['#0d6efd', '#20c997', '#ffc107', '#d63384', '#6f42c1', '#adb5bd'],
    };

    const pie_chart = new ApexCharts(document.querySelector('#pie-chart'), pie_chart_options);
    pie_chart.render();

    //-----------------
    // - END PIE CHART -
    //-----------------
  </script>
  <!-- Laravel Echo for real-time notifications -->
  <script src="https://unpkg.com/echo-laravel@1.15.3/dist/echo.js"></script>
  <script>
    // Initialize Echo with Reverb (adjust config as needed)
    window.Echo = new Echo({
      broadcaster: 'reverb',
      key: '<?php echo e(config('
      broadcasting.connections.reverb.key ')); ?>',
      wsHost: window.location.hostname,
      wsPort: 8082,
      wssPort: 8082,
      forceTLS: false,
      enabledTransports: ['ws', 'wss'],
    });

    // Listen for incoming messages and show notification
    document.addEventListener('DOMContentLoaded', function() {
      const userId = {
        {
          auth() - > id()
        }
      };
      Echo.private(`message.${userId}`)
        .listen('.message.sent', (e) => {
          // Show toast notification
          showToast(`New message from ${e.message.sender.name}: ${e.message.message.substring(0, 50)}...`);

          // Update messages badge if present
          const badge = document.querySelector('.navbar-badge.badge.text-bg-danger');
          if (badge) {
            const currentCount = parseInt(badge.textContent) || 0;
            badge.textContent = currentCount + 1;
          }
        });
    });

    // Function to show Bootstrap toast
    function showToast(message) {
      // Create toast if not exists
      let toastElement = document.getElementById('messageToast');
      if (!toastElement) {
        toastElement = document.createElement('div');
        toastElement.id = 'messageToast';
        toastElement.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toastElement.setAttribute('role', 'alert');
        toastElement.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                `;
        document.body.appendChild(toastElement);

        const toast = new bootstrap.Toast(toastElement);
        toast.show();
      } else {
        // Update and show existing toast
        toastElement.querySelector('.toast-body').textContent = message;
        const toast = new bootstrap.Toast(toastElement);
        toast.show();
      }
    }
  </script>
  <?php if (! (request()->routeIs('messages.*'))): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php if(session('forbidden')): ?>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'error',
        title: 'Akses ditolak',
        text: <?php echo json_encode(session('forbidden'), 15, 512) ?>,
        confirmButtonText: 'OK'
      });
    });
  </script>
  <?php endif; ?>
  <?php if(session('success')): ?>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: <?php echo json_encode(session('success'), 15, 512) ?>,
        timer: 2200,
        showConfirmButton: false
      });
    });
  </script>
  <?php endif; ?>
  <?php if(session('error')): ?>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: <?php echo json_encode(session('error'), 15, 512) ?>,
        confirmButtonText: 'OK'
      });
    });
  </script>
  <?php endif; ?>
  <?php if(session('warning')): ?>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'warning',
        title: 'Peringatan',
        text: <?php echo json_encode(session('warning'), 15, 512) ?>,
        confirmButtonText: 'OK'
      });
    });
  </script>
  <?php endif; ?>
  <?php if(session('info')): ?>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'info',
        title: 'Informasi',
        text: <?php echo json_encode(session('info'), 15, 512) ?>,
        confirmButtonText: 'OK'
      });
    });
  </script>
  <?php endif; ?>
  <?php if(session('status')): ?>
  <script>
    window.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        icon: 'info',
        title: 'Status',
        text: <?php echo json_encode(session('status'), 15, 512) ?>,
        confirmButtonText: 'OK'
      });
    });
  </script>
  <?php endif; ?>
  <?php endif; ?>
  <?php
  $brandLocation = auth()->check() ? auth()->user()->location : null;
  ?>
  <?php if($brandLocation && $brandLocation->custom_js_url): ?>
  <script src="<?php echo e($brandLocation->custom_js_url); ?>"></script>
  <?php endif; ?>
  <?php echo $__env->yieldContent('scripts'); ?>
  <?php echo $__env->yieldPushContent('scripts'); ?>
  <!--end::Script-->
</body>
<!--end::Body-->

</html>
<?php /**PATH D:\www\kantorapp\resources\views/layouts/app.blade.php ENDPATH**/ ?>