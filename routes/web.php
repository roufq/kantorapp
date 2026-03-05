<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskProgressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MasterTaskController;
use App\Http\Controllers\LocationChangeRequestController;
use Illuminate\Support\Facades\Notification;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::get('/login', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');
    $cacheKey = 'login:lock:' . md5(strtolower($request->email));
    $lockSeconds = 900; // 15 menit lockout
    $maxAttempts = 5;

    // Cek apakah sedang terkunci
    if (Cache::has($cacheKey . ':locked')) {
        return back()->withErrors(['email' => 'Akun dikunci sementara karena terlalu banyak percobaan. Coba lagi dalam beberapa menit.']);
    }

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Reset hitungan gagal
        Cache::forget($cacheKey . ':count');
        Cache::forget($cacheKey . ':locked');

        // Invalidate other sessions
        \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
            ->where('user_id', $user->id)
            ->where('id', '!=', $request->session()->getId())
            ->delete();

        // Non Super Admin/HR must have a location to login
        if (is_null($user->location_id) && !$user->hasRole('Super Admin') && !$user->hasRole('HR')) {
            Auth::logout();
            return back()->withErrors(['email' => 'This account is not assigned to any location.']);
        }

        // Require verified email only if enabled
        if (env('EMAIL_VERIFICATION_ENABLED', false)) {
            if (!$user->hasVerifiedEmail()) {
                try { $user->sendEmailVerificationNotification(); } catch (\Throwable $e) {}
                return redirect()->route('verification.notice');
            }
        }

        // Check if 2FA is required
        if ($user->hasTwoFactorEnabled()) {
            // Store login attempt and redirect to 2FA
            session(['2fa_user_id' => $user->id]);
            return redirect()->route('2fa.challenge');
        }

        return redirect()->intended('/dashboard');
    }

    // Gagal login: tingkatkan counter, lock jika perlu
    $count = Cache::increment($cacheKey . ':count', 1);
    if ($count === 1) {
        Cache::put($cacheKey . ':count', 1, $lockSeconds);
    }
    if ($count >= $maxAttempts) {
        Cache::put($cacheKey . ':locked', true, $lockSeconds);
    }

    return back()->withErrors(['email' => 'Email atau password salah.']);
})->name('login.post')->middleware('throttle:5,1');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    // Mark all notifications as read
    Route::post('/notifications/read', function (Request $request) {
        $user = $request->user();
        $user?->unreadNotifications?->markAsRead();
        return response()->json(['ok' => true]);
    })->name('notifications.read');

    // Email Verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verified.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // Two-Factor Authentication Routes
    Route::get('/2fa/setup', [App\Http\Controllers\TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/setup', [App\Http\Controllers\TwoFactorController::class, 'setup'])->name('2fa.setup.post');
    Route::get('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'showVerify'])->name('2fa.verify');
    Route::post('/2fa/verify', [App\Http\Controllers\TwoFactorController::class, 'verify'])->name('2fa.verify.post');
    Route::get('/2fa/verify-app', [App\Http\Controllers\TwoFactorController::class, 'showVerifyApp'])->name('2fa.verify-app');
    Route::post('/2fa/disable', [App\Http\Controllers\TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::delete('/2fa/disable', [App\Http\Controllers\TwoFactorController::class, 'disable']);

    // Profile
    Route::get('/profile', [App\Http\Controllers\UserController::class, 'profile'])->name('profile.show');
    Route::post('/profile/photo', [App\Http\Controllers\UserController::class, 'updatePhoto'])->name('profile.photo');
    Route::post('/profile/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('profile.password.update');
    Route::delete('/profile/sessions/{sessionId}', [App\Http\Controllers\UserController::class, 'logoutSession'])->name('profile.session.logout');
});

// 2FA Challenge route (before auth middleware)
Route::get('/2fa/challenge', function () {
    if (!session('2fa_user_id')) {
        return redirect('/login');
    }

    $user = \App\Models\User::find(session('2fa_user_id'));
    if (!$user || !$user->hasTwoFactorEnabled()) {
        session()->forget('2fa_user_id');
        return redirect('/login');
    }

    // Log the user in temporarily for the 2FA process
    Auth::login($user);

    return redirect()->route('2fa.verify');
})->name('2fa.challenge');

// Conditionally apply 'verified' middleware based on env flag
$verifiedMw = env('EMAIL_VERIFICATION_ENABLED', false) ? ['verified'] : [];
Route::middleware(array_merge(['auth', App\Http\Middleware\TwoFactorMiddleware::class], $verifiedMw))->group(function () {
    // Weekly Offs, Holidays, Leaves
    Route::get('/holidays', [App\Http\Controllers\HolidayController::class, 'index'])->middleware('role:Super Admin,Admin Lokasi')->name('holidays.index');
    Route::post('/holidays', [App\Http\Controllers\HolidayController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi')->name('holidays.store');
    Route::delete('/holidays/{holiday}', [App\Http\Controllers\HolidayController::class, 'destroy'])->middleware('role:Super Admin,Admin Lokasi')->name('holidays.destroy');

    Route::get('/weekly-offs', [App\Http\Controllers\WeeklyOffController::class, 'index'])->middleware('role:Super Admin,Admin Lokasi')->name('weekly-offs.index');
    Route::post('/weekly-offs', [App\Http\Controllers\WeeklyOffController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi')->name('weekly-offs.store');
    Route::delete('/weekly-offs/{weeklyOff}', [App\Http\Controllers\WeeklyOffController::class, 'destroy'])->middleware('role:Super Admin,Admin Lokasi')->name('weekly-offs.destroy');

    Route::get('/leaves', [App\Http\Controllers\LeaveController::class, 'index'])->name('leaves.index');
    Route::post('/leaves', [App\Http\Controllers\LeaveController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('leaves.store');
    Route::patch('/leaves/{leave}/status', [App\Http\Controllers\LeaveController::class, 'updateStatus'])->middleware('role:Super Admin,Admin Lokasi')->name('leaves.updateStatus');
    Route::get('/rosters/{roster}/export', [App\Http\Controllers\ShiftRosterController::class, 'export'])->name('shifts.rosters.export');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/kpi', [App\Http\Controllers\KpiController::class, 'index'])
        ->middleware('role:Super Admin,Admin Lokasi,HR,Karyawan')
        ->name('kpi.index');
    Route::get('/kpi/export', [App\Http\Controllers\KpiController::class, 'export'])
        ->middleware('role:Super Admin,Admin Lokasi,HR,Karyawan')
        ->name('kpi.export');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->middleware('role:Super Admin')->name('messages.destroy');

    // Employee Tasks (employee_tasks table)
    Route::get('/tasks', [TaskController::class, 'index'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.store');
    Route::get('/tasks/create-self', [TaskController::class, 'createSelf'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.create.self');
    Route::post('/tasks/store-self', [TaskController::class, 'storeSelf'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.store.self');
    Route::post('/tasks/{task}/approve-creation', [TaskController::class, 'approveCreation'])->middleware('role:Super Admin,Admin Lokasi')->name('tasks.approvals.approve');
    Route::post('/tasks/{task}/reject-creation', [TaskController::class, 'rejectCreation'])->middleware('role:Super Admin,Admin Lokasi')->name('tasks.approvals.reject');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.destroy');
    Route::get('/tasks/{task}/download-photo', [TaskController::class, 'downloadPhoto'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.download.photo');
    Route::get('/tasks/{task}/download-document', [TaskController::class, 'downloadDocument'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.download.document');
    Route::get('/tasks/{task}/progress/create', [TaskProgressController::class, 'create'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.progress.create');
    Route::post('/tasks/{task}/progress', [TaskProgressController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.progress.store');
    Route::get('/task-progress/approvals', [TaskProgressController::class, 'approvals'])->middleware('role:Super Admin,Admin Lokasi')->name('tasks.progress.approvals');
    Route::resource('work-recaps', App\Http\Controllers\WorkRecapController::class)
        ->except(['show'])
        ->middleware('role:Super Admin,Admin Lokasi');
    Route::resource('work-targets', App\Http\Controllers\LocationWorkTargetController::class)
        ->except(['show'])
        ->middleware('role:Super Admin,Admin Lokasi');
    Route::post('/task-progress/{progressUpdate}/approve', [TaskProgressController::class, 'approve'])->middleware('role:Super Admin,Admin Lokasi')->name('tasks.progress.approve');
    Route::post('/task-progress/{progressUpdate}/reject', [TaskProgressController::class, 'reject'])->middleware('role:Super Admin,Admin Lokasi')->name('tasks.progress.reject');
    Route::get('/task-progress/{progressUpdate}/download/{type}', [TaskProgressController::class, 'downloadAttachment'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('tasks.progress.download');

    // Master Tasks (master_tasks table) - only masters can access their own tasks
    Route::get('/master-tasks', [MasterTaskController::class, 'index'])->middleware('role:Super Admin')->name('master-tasks.index');
    Route::get('/master-tasks/create', [MasterTaskController::class, 'create'])->middleware('role:Super Admin')->name('master-tasks.create');
    Route::post('/master-tasks', [MasterTaskController::class, 'store'])->middleware('role:Super Admin')->name('master-tasks.store');
    Route::get('/master-tasks/create-self', [MasterTaskController::class, 'createSelf'])->middleware('role:Super Admin')->name('master-tasks.create.self');
    Route::post('/master-tasks/store-self', [MasterTaskController::class, 'storeSelf'])->middleware('role:Super Admin')->name('master-tasks.store.self');
    Route::get('/master-tasks/{masterTask}', [MasterTaskController::class, 'show'])->middleware('role:Super Admin')->name('master-tasks.show');
    Route::get('/master-tasks/{masterTask}/edit', [MasterTaskController::class, 'edit'])->middleware('role:Super Admin')->name('master-tasks.edit');
    Route::patch('/master-tasks/{masterTask}', [MasterTaskController::class, 'update'])->middleware('role:Super Admin')->name('master-tasks.update');
    Route::delete('/master-tasks/{masterTask}', [MasterTaskController::class, 'destroy'])->middleware('role:Super Admin')->name('master-tasks.destroy');
    Route::get('/master-tasks/{masterTask}/download-photo', [MasterTaskController::class, 'downloadPhoto'])->middleware('role:Super Admin')->name('master-tasks.download.photo');
    Route::get('/master-tasks/{masterTask}/download-document', [MasterTaskController::class, 'downloadDocument'])->middleware('role:Super Admin')->name('master-tasks.download.document');

    // Users: authorize via gates to allow Super Admin and Admin Lokasi within location
    Route::resource('users', UserController::class);
    Route::patch('/users/{user}/transfer', [UserController::class, 'transfer'])->name('users.transfer');
    Route::post('/users/{user}/promote-to-location-admin', [UserController::class, 'promoteToLocationAdmin'])->middleware('role:Super Admin')->name('users.promote.location-admin');
    Route::post('/users/{user}/promote-to-hr', [UserController::class, 'promoteToHr'])->middleware('role:Super Admin')->name('users.promote.hr');
    Route::post('/users/{user}/demote-to-employee', [UserController::class, 'demoteToEmployee'])->middleware('role:Super Admin')->name('users.demote.employee');
    // Removed masters management; Super Admin role manages all directly
    Route::resource('divisions', DivisionController::class)->middleware('role:Super Admin');
    Route::resource('jobdesks', App\Http\Controllers\JobdeskController::class)
        ->middleware('role:Super Admin,HR');
    Route::resource('jobdesk-targets', App\Http\Controllers\JobdeskOutputTargetController::class)
        ->middleware('role:Super Admin,HR');
    Route::resource('approval-rules', App\Http\Controllers\ApprovalRuleController::class)
        ->middleware('role:Super Admin,HR');
    Route::resource('employee-positions', App\Http\Controllers\EmployeePositionHistoryController::class)
        ->middleware('role:Super Admin,HR');
    Route::resource('employee-transfers', App\Http\Controllers\EmployeeTransferController::class)
        ->middleware('role:Super Admin,HR');
    Route::resource('employee-contracts', App\Http\Controllers\EmployeeContractController::class)
        ->middleware('role:Super Admin,HR');
    Route::prefix('jobdesks/{jobdesk}')->name('jobdesks.')->middleware('role:Super Admin,HR')->group(function () {
        Route::get('task-catalogs', [App\Http\Controllers\TaskCatalogController::class, 'index'])->name('catalogs.index');
        Route::get('task-catalogs/create', [App\Http\Controllers\TaskCatalogController::class, 'create'])->name('catalogs.create');
        Route::post('task-catalogs', [App\Http\Controllers\TaskCatalogController::class, 'store'])->name('catalogs.store');
        Route::get('task-catalogs/{catalog}/edit', [App\Http\Controllers\TaskCatalogController::class, 'edit'])->name('catalogs.edit');
        Route::put('task-catalogs/{catalog}', [App\Http\Controllers\TaskCatalogController::class, 'update'])->name('catalogs.update');
        Route::delete('task-catalogs/{catalog}', [App\Http\Controllers\TaskCatalogController::class, 'destroy'])->name('catalogs.destroy');
        Route::get('assignments', [App\Http\Controllers\EmployeeJobdeskAssignmentController::class, 'index'])->name('assignments.index');
        Route::post('assignments', [App\Http\Controllers\EmployeeJobdeskAssignmentController::class, 'store'])->name('assignments.store');
        Route::delete('assignments/{assignment}', [App\Http\Controllers\EmployeeJobdeskAssignmentController::class, 'destroy'])->name('assignments.destroy');
    });

    Route::resource('locations', App\Http\Controllers\LocationController::class)->middleware('role:Super Admin');
    // Location Settings (Super Admin and Admin Lokasi for own location)
    Route::get('/locations/{location}/settings', [App\Http\Controllers\LocationController::class, 'settings'])->middleware('role:Super Admin,Admin Lokasi')->name('locations.settings');
    Route::patch('/locations/{location}/settings', [App\Http\Controllers\LocationController::class, 'updateSettings'])->middleware('role:Super Admin,Admin Lokasi')->name('locations.settings.update');
    Route::get('/shifts/scheduler', [App\Http\Controllers\ShiftController::class, 'schedulerForm'])->middleware('role:Super Admin')->name('shifts.scheduler');
    Route::post('/shifts/scheduler', [App\Http\Controllers\ShiftController::class, 'schedulerGenerate'])->middleware('role:Super Admin')->name('shifts.scheduler.generate');

    // Weekly Rosters (didefinisikan sebelum resource shifts untuk menghindari bentrok shifts/{shift})
    Route::get('/shifts/rosters/calendar', [App\Http\Controllers\ShiftRosterController::class, 'calendar'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('shifts.rosters.calendar');
    Route::prefix('shifts')->name('shifts.')->middleware('role:Super Admin,Admin Lokasi')->group(function () {
        Route::resource('rosters', App\Http\Controllers\ShiftRosterController::class);
    });

    // Legacy routes redirect ke scheduler (batasi parameter agar tidak menelan '/rosters')
    Route::resource('shifts', App\Http\Controllers\ShiftController::class, [
        'except' => ['index']
    ])->middleware('role:Super Admin')->whereNumber('shift');

    // Location Shifts Management
    Route::resource('location-shifts', App\Http\Controllers\LocationShiftController::class, [
        'parameters' => [
            'location-shifts' => 'location'
        ]
    ])->middleware('role:Super Admin');
    Route::post('/location-shifts/{location}/attach-shift', [App\Http\Controllers\LocationShiftController::class, 'attachShift'])->middleware('role:Super Admin')->name('location-shifts.attach-shift');
    Route::delete('/location-shifts/{location}/detach-shift/{shift}', [App\Http\Controllers\LocationShiftController::class, 'detachShift'])->middleware('role:Super Admin')->name('location-shifts.detach-shift');

    Route::get('/attendance/checkin', [AttendanceController::class, 'showCheckIn'])->name('attendance.checkin');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin.post');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
    Route::patch('/attendance/{id}/approval', [AttendanceController::class, 'updateApproval'])->name('attendance.update.approval');

    // Reports (Laporan)
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/employee-performance', [App\Http\Controllers\EmployeePerformanceReportController::class, 'index'])
        ->middleware('role:Super Admin,Admin Lokasi,HR,Karyawan')
        ->name('reports.employee-performance');
    Route::get('/reports/employee-performance/export', [App\Http\Controllers\EmployeePerformanceReportController::class, 'export'])
        ->middleware('role:Super Admin,Admin Lokasi,HR,Karyawan')
        ->name('reports.employee-performance.export');
    Route::get('/reports/create', [App\Http\Controllers\ReportController::class, 'create'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('reports.create');
    Route::post('/reports', [App\Http\Controllers\ReportController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('reports.store');
    Route::get('/reports/{report}', [App\Http\Controllers\ReportController::class, 'show'])->name('reports.show');
    Route::get('/reports/{report}/edit', [App\Http\Controllers\ReportController::class, 'edit'])->middleware('role:Super Admin|Admin Lokasi')->name('reports.edit');
    Route::put('/reports/{report}', [App\Http\Controllers\ReportController::class, 'update'])->middleware('role:Super Admin|Admin Lokasi')->name('reports.update');
    Route::delete('/reports/{report}', [App\Http\Controllers\ReportController::class, 'destroy'])->middleware('role:Super Admin|Admin Lokasi')->name('reports.destroy');
    Route::patch('/reports/{report}/approve', [App\Http\Controllers\ReportController::class, 'approve'])->middleware('role:Super Admin|Admin Lokasi')->name('reports.approve');
    Route::get('/report-attachments/{attachment}/download', [App\Http\Controllers\ReportController::class, 'downloadAttachment'])->name('reports.attachments.download');

    // Task Slots (structure & approval)
    Route::prefix('tasks')->group(function () {
        Route::post('{task}/slots', [App\Http\Controllers\TaskSlotController::class, 'store'])->name('tasks.slots.store')->middleware('role:Super Admin,Admin Lokasi');
        Route::patch('{task}/slots/{slot}', [App\Http\Controllers\TaskSlotController::class, 'update'])->name('tasks.slots.update')->middleware('role:Super Admin,Admin Lokasi');
        Route::delete('{task}/slots/{slot}', [App\Http\Controllers\TaskSlotController::class, 'destroy'])->name('tasks.slots.destroy')->middleware('role:Super Admin,Admin Lokasi');
    });
    Route::post('/task-slots/{slot}/submit', [App\Http\Controllers\TaskSlotController::class, 'submit'])->name('task-slots.submit')->middleware('auth');
    Route::post('/task-slots/{slot}/approve', [App\Http\Controllers\TaskSlotController::class, 'approve'])->name('task-slots.approve')->middleware('role:Super Admin,Admin Lokasi');
    Route::post('/task-slots/{slot}/reject', [App\Http\Controllers\TaskSlotController::class, 'reject'])->name('task-slots.reject')->middleware('role:Super Admin,Admin Lokasi');
    Route::post('/tasks/{task}/approve-slots', [App\Http\Controllers\TaskSlotController::class, 'approveTask'])->name('tasks.approve-slots')->middleware('role:Super Admin,Admin Lokasi');
    Route::post('/tasks/{task}/reject-slots', [App\Http\Controllers\TaskSlotController::class, 'rejectTask'])->name('tasks.reject-slots')->middleware('role:Super Admin,Admin Lokasi');

    // Overtime Requests
    Route::get('/overtime', [App\Http\Controllers\OvertimeController::class, 'index'])->name('overtime.index');
    Route::get('/overtime/create', [App\Http\Controllers\OvertimeController::class, 'create'])->name('overtime.create');
    Route::post('/overtime', [App\Http\Controllers\OvertimeController::class, 'store'])->name('overtime.store');
    Route::get('/overtime/export', [App\Http\Controllers\OvertimeController::class, 'export'])->name('overtime.export');
    Route::get('/overtime-report', [App\Http\Controllers\OvertimeController::class, 'report'])->name('overtime.report');
    Route::get('/overtime/{overtime}/edit', [App\Http\Controllers\OvertimeController::class, 'edit'])->name('overtime.edit');
    Route::put('/overtime/{overtime}', [App\Http\Controllers\OvertimeController::class, 'update'])->name('overtime.update');
    Route::delete('/overtime/{overtime}', [App\Http\Controllers\OvertimeController::class, 'destroy'])->name('overtime.destroy');
    Route::get('/overtime/{overtime}', [App\Http\Controllers\OvertimeController::class, 'show'])->name('overtime.show');
    Route::patch('/overtime/{overtime}/approve', [App\Http\Controllers\OvertimeController::class, 'approve'])->middleware('role:Super Admin|Admin Lokasi')->name('overtime.approve');

    // Location Admin Tasks (Super Admin and Admin Lokasi)
    Route::resource('location-admin-tasks', App\Http\Controllers\LocationAdminTaskController::class)->middleware('role:Super Admin,Admin Lokasi');
    Route::get('/location-admin-tasks/{task}/download-photo', [App\Http\Controllers\LocationAdminTaskController::class, 'downloadPhoto'])->middleware('role:Super Admin,Admin Lokasi')->name('location-admin-tasks.download.photo');
    Route::get('/location-admin-tasks/{task}/download-document', [App\Http\Controllers\LocationAdminTaskController::class, 'downloadDocument'])->middleware('role:Super Admin,Admin Lokasi')->name('location-admin-tasks.download.document');

    // Location Admin Management (Super Admin only)
    Route::resource('location-admins', App\Http\Controllers\LocationAdminController::class)->middleware('role:Super Admin');

    // Location Change Requests
    Route::get('/location-change-requests', [LocationChangeRequestController::class, 'index'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('location-change-requests.index');
    Route::get('/location-change-requests/create', [LocationChangeRequestController::class, 'create'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('location-change-requests.create');
    Route::post('/location-change-requests', [LocationChangeRequestController::class, 'store'])->middleware('role:Super Admin,Admin Lokasi,Karyawan')->name('location-change-requests.store');
    Route::patch('/location-change-requests/{locationChangeRequest}/status', [LocationChangeRequestController::class, 'updateStatus'])->middleware('role:Super Admin,Admin Lokasi')->name('location-change-requests.updateStatus');

    // Super Admin location selection (session-scoped)
    Route::middleware('role:Super Admin')->group(function () {
        Route::get('/location-selection', [App\Http\Controllers\LocationSelectionController::class, 'index'])->name('location-selection.index');
        Route::post('/location-selection', [App\Http\Controllers\LocationSelectionController::class, 'store'])->name('location-selection.store');
    });

    // Attendance reports
    Route::get('/attendance/absences', [AttendanceController::class, 'absences'])->name('attendance.absences');
    Route::get('/attendance/recap', [AttendanceController::class, 'recap'])->name('attendance.recap');
    Route::get('/attendance/recap/export', [AttendanceController::class, 'exportRecap'])->name('attendance.recap.export');
    // Employees (Super Admin and Admin Lokasi)
    Route::resource('karyawans', App\Http\Controllers\EmployeeController::class)->middleware('role:Super Admin,Admin Lokasi');
});
