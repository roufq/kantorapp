@extends('layouts.appnew')

@push('styles')
<style>
    /* Executive Dashboard Styles */
    .stat-card {
        padding: 1.5rem;
        border-radius: var(--card-radius);
        background: #ffffff;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 30px rgba(0,0,0,0.04) !important;
        border-color: var(--primary-light);
    }
    
    .stat-icon {
        width: 42px; height: 42px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
    }
    .icon-primary { background: var(--soft-mint); color: var(--soft-mint-text); }
    .icon-info { background: var(--soft-sky); color: var(--soft-sky-text); }
    .icon-warning { background: var(--soft-honey); color: var(--soft-honey-text); }
    .icon-danger { background: var(--soft-rose); color: var(--soft-rose-text); }

    .stat-label { font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 2rem; font-weight: 800; color: var(--text-main); margin: 0.25rem 0; }
    .stat-meta { font-size: 0.8rem; font-weight: 600; display: flex; align-items: center; gap: 4px; }

    /* Performance Charts */
    .chart-container { position: relative; height: 240px; width: 100%; }
    
    /* List Components */
    .dashboard-list { display: flex; flex-direction: column; gap: 0.75rem; }
    .list-item {
        padding: 1rem;
        border-radius: 16px;
        border: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s;
    }
    .list-item:hover { background: #fcfdfe; border-color: #f1f5f9; }
    .avatar-sm { width: 36px; height: 36px; border-radius: 10px; object-fit: cover; }
    
    /* Section Headings */
    .section-title { font-size: 1.1rem; font-weight: 800; color: var(--text-main); margin-bottom: 1.5rem; display: flex; align-items: center; gap: 8px; }

    /* Custom Scrollbar for list bodies */
    .scroll-body { max-height: 400px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #f1f5f9 transparent; }
    .scroll-body::-webkit-scrollbar { width: 4px; }
    .scroll-body::-webkit-scrollbar-thumb { background: #f1f5f9; border-radius: 10px; }
</style>
@endpush

@section('content')
<!-- Dashboard Header -->
<div class="row mb-4 align-items-center">
    <div class="col-md-8">
        <h1 class="fw-bold mb-1" style="font-size: 2.2rem; letter-spacing: -0.5px; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            {{ __('Executive Overview') }}
        </h1>
        <p class="text-muted mb-0 fw-500">
            {{ __('Welcome back,') }} <span class="text-primary fw-bold">{{ $user->name }}</span>. {{ __('Here is what is happening across your workplace today.') }}
        </p>
    </div>
    <div class="col-md-4 text-md-end mt-3 mt-md-0">
        <div class="badge badge-indigo border-0 py-2 px-3 shadow-none">
            <i class="mdi mdi-calendar-range me-2"></i> {{ now()->format('d M Y') }}
        </div>
    </div>
</div>

<!-- Key Performance Indicators -->
<div class="row g-3 mb-4">
    <!-- Total Workforce -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-icon icon-primary"><i class="mdi mdi-account-group"></i></div>
                <div class="stat-label d-none d-md-block">{{ __('Total Workforce') }}</div>
                <div class="stat-label d-md-none">{{ __('Staff') }}</div>
                <div class="stat-value" style="font-size: clamp(1.5rem, 4vw, 2.2rem);">{{ $chartMetrics['total_employees'] }}</div>
            </div>
        </div>
    </div>

    <!-- Attendance Rate -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-icon icon-info"><i class="mdi mdi-calendar-check"></i></div>
                <div class="stat-label d-none d-md-block">{{ __('Daily Attendance') }}</div>
                <div class="stat-label d-md-none">{{ __('Present') }}</div>
                @php
                    $attRate = $chartMetrics['total_employees'] > 0 
                        ? round(($chartMetrics['checked_in_today'] / $chartMetrics['total_employees']) * 100, 1) 
                        : 0;
                @endphp
                <div class="stat-value" style="font-size: clamp(1.5rem, 4vw, 2.2rem);">{{ $attRate }}%</div>
            </div>
        </div>
    </div>

    <!-- Task Completion -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-icon icon-warning"><i class="mdi mdi-progress-check"></i></div>
                <div class="stat-label d-none d-md-block">{{ __('Task Velocity') }}</div>
                <div class="stat-label d-md-none">{{ __('Tasks') }}</div>
                @php
                    $taskRate = $chartMetrics['total_tasks'] > 0 
                        ? round(($chartMetrics['completed_tasks'] / $chartMetrics['total_tasks']) * 100, 1) 
                        : 0;
                @endphp
                <div class="stat-value" style="font-size: clamp(1.5rem, 4vw, 2.2rem);">{{ $taskRate }}%</div>
            </div>
        </div>
    </div>

    <!-- Messages/Requests -->
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div>
                <div class="stat-icon icon-danger"><i class="mdi mdi-bell-ring-outline"></i></div>
                <div class="stat-label d-none d-md-block">{{ __('Action Required') }}</div>
                <div class="stat-label d-md-none">{{ __('Alerts') }}</div>
                <div class="stat-value" style="font-size: clamp(1.5rem, 4vw, 2.2rem);">{{ $unreadMessages + $absencesTodayCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Visual Analytics -->
    <div class="col-xl-8 col-lg-12">
        <div class="card shadow-sm border-light h-100">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="section-title mb-0"><i class="mdi mdi-chart-line text-primary"></i> {{ __('Workplace Analytics') }}</h5>
                </div>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="mainDashboardChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Side Intelligence Panes -->
    <div class="col-xl-4 col-lg-12">
        <div class="row g-4 h-100">
            <!-- Active Personnel -->
            <div class="col-12 col-md-6 col-xl-12">
                <div class="card shadow-sm border-light h-100">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h5 class="section-title mb-0"><i class="mdi mdi-account-star text-info"></i> {{ __('Recent Assignments') }}</h5>
                    </div>
                    <div class="card-body p-4 scroll-body">
                         <div class="dashboard-list">
                            @forelse($recentAssignments->take(4) as $assign)
                                <div class="list-item">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <div class="text-dark fw-bold small text-truncate">{{ $assign->user->name }}</div>
                                        <div class="smaller text-muted">{{ optional($assign->shift)->name ?? 'General' }} | {{ \Carbon\Carbon::parse($assign->date)->format('d M') }}</div>
                                    </div>
                                    <div class="smaller fw-bold text-primary">{{ $assign->shift ? $assign->shift->start_time : '-' }}</div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted smaller mt-2">No recent assignments found.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Timeline -->
            <div class="col-12 col-md-6 col-xl-12">
                <div class="card shadow-sm border-light" style="background: var(--soft-mint);">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-mint-text fw-bold mb-3">{{ __('Need support?') }}</h6>
                        <a href="{{ route('reports.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold shadow-soft w-100">
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

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Main Performance Chart
        const ctx = document.getElementById('mainDashboardChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Productivity',
                    data: [65, 78, 90, 85, 95, 40, 30],
                    borderColor: '#10b981',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: (context) => {
                        const bg = ctx.createLinearGradient(0, 0, 0, 400);
                        bg.addColorStop(0, 'rgba(16, 185, 129, 0.1)');
                        bg.addColorStop(1, 'rgba(16, 185, 129, 0)');
                        return bg;
                    },
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#fff',
                        titleColor: '#0f172a',
                        bodyColor: '#64748b',
                        borderColor: '#f1f5f9',
                        borderWidth: 1,
                        padding: 12,
                        boxPadding: 4,
                        cornerRadius: 12,
                        titleFont: { size: 13, weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#f1f5f9' },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#94a3b8', font: { size: 11 } }
                    }
                }
            }
        });
    });
</script>
@endsection
