@extends('layouts.appnew')

@section('content')
<!-- Dashboard Header -->
<div class="row mb-5 align-items-center">
    <div class="col-md-9">
        <div class="d-flex align-items-center gap-3 mb-2">
            <span class="badge bg-primary-light text-primary py-1 px-2 rounded-3 small fw-bold">{{ __('Executive Dashboard') }}</span>
            <span class="text-muted opacity-50">|</span>
            <span class="text-muted small fw-medium"><i class="mdi mdi-clock-outline me-1"></i> {{ __('Last updated 5 mins ago') }}</span>
        </div>
        <h1 class="fw-800 mb-2" style="font-size: 2.8rem; letter-spacing: -1.5px; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Executive Overview') }}
        </h1>
        <p class="text-muted mb-0 fs-5 fw-medium">
            {{ __('Welcome back,') }} <span class="text-dark fw-bold">{{ $user->name }}</span>. {{ __('Here is what is happening across your workplace today.') }}
        </p>
    </div>
    <div class="col-md-3 text-md-end mt-4 mt-md-0">
        <div class="bg-white p-3 rounded-4 shadow-sm border border-light d-inline-flex align-items-center">
            <div class="stat-icon icon-info mb-0 me-3 shadow-none" style="width: 48px; height: 48px;">
                <i class="mdi mdi-calendar-blank"></i>
            </div>
            <div class="text-start">
                <div class="smallest text-muted fw-bold text-uppercase">{{ __('Current Date') }}</div>
                <div class="fw-bold text-dark">{{ now()->format('l, d M Y') }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="row g-4 mb-5">
    <!-- Total Workforce -->
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%);">
            <div>
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-icon icon-primary shadow-sm"><i class="mdi mdi-account-group"></i></div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill smaller">+2% <i class="mdi mdi-arrow-up"></i></span>
                </div>
                <div class="stat-label mt-2">{{ __('Total Workforce') }}</div>
                <div class="stat-value">{{ $chartMetrics['total_employees'] }}</div>
                <div class="stat-meta text-success">
                    <i class="mdi mdi-check-circle-outline"></i> {{ __('All active locations') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Rate -->
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%);">
            <div>
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-icon icon-info shadow-sm"><i class="mdi mdi-calendar-check"></i></div>
                    <span class="badge bg-info bg-opacity-10 text-info rounded-pill smaller">{{ __('Normal') }}</span>
                </div>
                <div class="stat-label mt-2">{{ __('Daily Attendance') }}</div>
                @php
                    $attRate = $chartMetrics['total_employees'] > 0 
                        ? round(($chartMetrics['checked_in_today'] / $chartMetrics['total_employees']) * 100, 1) 
                        : 0;
                @endphp
                <div class="stat-value">{{ $attRate }}%</div>
                <div class="stat-meta text-info">
                    <i class="mdi mdi-account-clock-outline"></i> {{ $chartMetrics['checked_in_today'] }} {{ __('Present today') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Task Completion -->
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(180deg, #ffffff 0%, #fffbeb 100%);">
            <div>
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-icon icon-warning shadow-sm"><i class="mdi mdi-progress-check"></i></div>
                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill smaller">-4% <i class="mdi mdi-arrow-down"></i></span>
                </div>
                <div class="stat-label mt-2">{{ __('Task Velocity') }}</div>
                @php
                    $taskRate = $chartMetrics['total_tasks'] > 0 
                        ? round(($chartMetrics['completed_tasks'] / $chartMetrics['total_tasks']) * 100, 1) 
                        : 0;
                @endphp
                <div class="stat-value">{{ $taskRate }}%</div>
                <div class="stat-meta text-warning">
                    <i class="mdi mdi-timer-sand"></i> {{ $chartMetrics['total_tasks'] - $chartMetrics['completed_tasks'] }} {{ __('Pending tasks') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Messages/Requests -->
    <div class="col-md-3">
        <div class="stat-card" style="background: linear-gradient(180deg, #ffffff 0%, #fff1f2 100%);">
            <div>
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-icon icon-danger shadow-sm"><i class="mdi mdi-bell-ring-outline"></i></div>
                    <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill smaller">{{ __('Priority') }}</span>
                </div>
                <div class="stat-label mt-2">{{ __('Action Required') }}</div>
                <div class="stat-value">{{ $unreadMessages + $absencesTodayCount }}</div>
                <div class="stat-meta text-danger">
                    <i class="mdi mdi-alert-circle-outline"></i> {{ $absencesTodayCount }} {{ __('Critical alerts') }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Visual Analytics -->
    <div class="col-xl-8 col-lg-12">
        <div class="card shadow-sm border-0 h-100 rounded-5 overflow-hidden">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="section-title mb-1"><i class="mdi mdi-chart-box-outline text-primary"></i> {{ __('Workplace Analytics') }}</h5>
                        <p class="smaller text-muted mb-0">{{ __('Weekly performance and productivity tracking') }}</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-light rounded-pill btn-sm fw-bold px-3 border border-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            {{ __('Last 7 Days') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4">
                            <li><a class="dropdown-item fw-medium" href="#">{{ __('Last 24 Hours') }}</a></li>
                            <li><a class="dropdown-item fw-medium" href="#">{{ __('Last 7 Days') }}</a></li>
                            <li><a class="dropdown-item fw-medium" href="#">{{ __('This Month') }}</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="chart-container" style="height: 340px;">
                    <canvas id="mainDashboardChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Intelligence Panes -->
    <div class="col-xl-4 col-lg-12">
        <div class="row g-4 h-100">
            <!-- Active Personnel -->
            <div class="col-12">
                <div class="card shadow-sm border-0 h-100 rounded-5">
                    <div class="card-header bg-white border-0 p-4 pb-1">
                        <h5 class="section-title mb-0"><i class="mdi mdi-flash-outline text-info"></i> {{ __('Live Assignments') }}</h5>
                    </div>
                    <div class="card-body p-4 pt-0 scroll-body" style="max-height: 400px;">
                         <div class="dashboard-list">
                            @forelse($recentAssignments->take(6) as $assign)
                                <div class="list-item border-light hover-shadow-sm">
                                    <div class="stat-icon icon-info mb-0" style="width: 34px; height: 34px; font-size: 0.9rem;">
                                        {{ substr($assign->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="text-dark fw-800 small text-truncate">{{ $assign->user->name }}</div>
                                        <div class="smallest text-muted">{{ optional($assign->shift)->name ?? 'General' }} <span class="mx-1">•</span> {{ \Carbon\Carbon::parse($assign->date)->format('M d') }}</div>
                                    </div>
                                    @php
                                        $startTime = $assign->shift ? \Carbon\Carbon::parse($assign->shift->start_time) : null;
                                        $isNow = $startTime && $startTime->isToday();
                                    @endphp
                                    <div class="badge {{ $isNow ? 'bg-success' : 'bg-light text-muted' }} rounded-pill smaller px-2">
                                        {{ $assign->shift ? $assign->shift->start_time : 'ASAP' }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 opacity-50">
                                    <i class="mdi mdi-inbox-outline fs-1"></i>
                                    <p class="text-muted smallest fw-bold mt-2">No live assignments</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Timeline -->
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="card-body p-4 text-center text-white">
                        <div class="stat-icon bg-white bg-opacity-30 text-white mx-auto mb-3" style="width: 56px; height: 56px; font-size: 1.8rem; border: 2px solid rgba(255,255,255,0.4);">
                            <i class="mdi mdi-file-document-check-outline"></i>
                        </div>
                        <h5 class="fw-800 mb-2 text-white">{{ __('Analytic Reports') }}</h5>
                        <p class="smaller opacity-90 mb-4 text-white">{{ __('Generate comprehensive workforce data instantly.') }}</p>
                        <a href="{{ route('reports.create') }}" class="btn btn-white rounded-pill px-4 fw-800 w-100 py-3 shadow-sm" style="color: #064e3b !important; letter-spacing: 0.5px;">
                            {{ __('Generate Report') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lower Section: Tasks & Schedule -->
<div class="row g-4 mt-2">
    <!-- Tasks Overview -->
    <div class="col-xl-7">
        <div class="card shadow-sm border-light">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="section-title mb-0"><i class="mdi mdi-checkbox-multiple-marked-circle text-success"></i> {{ __('Task Pulse') }}</h5>
                    <a href="{{ route('tasks.index') }}" class="smaller fw-bold text-primary text-decoration-none">{{ __('Manage All') }} <i class="mdi mdi-arrow-right"></i></a>
                </div>
            </div>
            <div class="card-body p-4 scroll-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>{{ __('Task') }}</th>
                                <th>{{ __('Assignee') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Progress') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tasks->take(5) as $task)
                                <tr onclick="window.location='{{ route('tasks.show', $task) }}'" style="cursor: pointer;">
                                    <td>
                                        <div class="text-dark fw-bold small text-truncate" style="max-width: 12rem;">{{ $task->title }}</div>
                                        <div class="smaller text-muted">{{ $task->due_date ? $task->due_date->diffForHumans() : 'No date' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="smaller fw-bold text-dark">{{ optional($task->assignee)->name ?? 'Unassigned' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $task->status === 'completed' ? 'badge-success' : 'badge-info' }} rounded-pill" style="font-size: 0.6rem !important;">
                                            {{ $task->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $task->progress ?? 0 }}%;"></div>
                                            </div>
                                            <span class="smaller fw-bold">{{ $task->progress ?? 0 }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted smaller">No active tasks found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Notices -->
    <div class="col-xl-5">
        <div class="card shadow-sm border-light h-100">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h5 class="section-title mb-0"><i class="mdi mdi-calendar-text text-warning"></i> {{ __('Upcoming Notices') }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="dashboard-list">
                    @forelse($upcomingNotices as $notice)
                        <div class="p-3 rounded-4 bg-light border border-light d-flex align-items-start gap-3">
                            <div class="p-2 rounded-3 bg-white border border-light text-center" style="min-width: 50px;">
                                <div class="smaller fw-bold text-muted">{{ \Carbon\Carbon::parse($notice['date'])->format('D') }}</div>
                                <div class="h5 mb-0 fw-800">{{ \Carbon\Carbon::parse($notice['date'])->format('d') }}</div>
                            </div>
                            <div class="flex-grow-1">
                                @foreach($notice['labels'] as $label)
                                    <div class="text-dark fw-bold small">{{ $label }}</div>
                                @endforeach
                                <div class="smaller text-muted mt-1">{{ \Carbon\Carbon::parse($notice['date'])->format('M Y') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="mdi mdi-calendar-blank-outline fs-1 text-muted opacity-25"></i>
                            <p class="text-muted smaller mt-2">No upcoming holidays or events.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

