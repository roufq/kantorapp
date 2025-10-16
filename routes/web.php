<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\MasterTaskController;

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
    if (Auth::attempt($credentials)) {
        return redirect()->intended('/dashboard');
    }
    return back()->withErrors(['email' => 'Invalid credentials']);
})->name('login.post');

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.read');
    Route::delete('/messages/{id}', [MessageController::class, 'destroy'])->middleware('role:master')->name('messages.destroy');

    // Employee Tasks (employee_tasks table)
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/tasks/create-self', [TaskController::class, 'createSelf'])->name('tasks.create.self');
    Route::post('/tasks/store-self', [TaskController::class, 'storeSelf'])->name('tasks.store.self');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/tasks/{task}/download-photo', [TaskController::class, 'downloadPhoto'])->name('tasks.download.photo');
    Route::get('/tasks/{task}/download-document', [TaskController::class, 'downloadDocument'])->name('tasks.download.document');

    // Master Tasks (master_tasks table) - only masters can access their own tasks
    Route::get('/master-tasks', [MasterTaskController::class, 'index'])->middleware('role:master')->name('master-tasks.index');
    Route::get('/master-tasks/create', [MasterTaskController::class, 'create'])->middleware('role:master')->name('master-tasks.create');
    Route::post('/master-tasks', [MasterTaskController::class, 'store'])->middleware('role:master')->name('master-tasks.store');
    Route::get('/master-tasks/create-self', [MasterTaskController::class, 'createSelf'])->middleware('role:master')->name('master-tasks.create.self');
    Route::post('/master-tasks/store-self', [MasterTaskController::class, 'storeSelf'])->middleware('role:master')->name('master-tasks.store.self');
    Route::get('/master-tasks/{masterTask}', [MasterTaskController::class, 'show'])->middleware('role:master')->name('master-tasks.show');
    Route::get('/master-tasks/{masterTask}/edit', [MasterTaskController::class, 'edit'])->middleware('role:master')->name('master-tasks.edit');
    Route::patch('/master-tasks/{masterTask}', [MasterTaskController::class, 'update'])->middleware('role:master')->name('master-tasks.update');
    Route::delete('/master-tasks/{masterTask}', [MasterTaskController::class, 'destroy'])->middleware('role:master')->name('master-tasks.destroy');
    Route::get('/master-tasks/{masterTask}/download-photo', [MasterTaskController::class, 'downloadPhoto'])->middleware('role:master')->name('master-tasks.download.photo');
    Route::get('/master-tasks/{masterTask}/download-document', [MasterTaskController::class, 'downloadDocument'])->middleware('role:master')->name('master-tasks.download.document');

    Route::resource('users', UserController::class)->middleware('role:master');
    Route::resource('masters', MasterController::class)->middleware('role:master');
    Route::resource('divisions', DivisionController::class)->middleware('role:master');
    Route::resource('karyawans', EmployeeController::class)->middleware('role:master');
    Route::resource('office-locations', App\Http\Controllers\OfficeLocationController::class)->middleware('role:master');
    Route::get('/attendance/checkin', [AttendanceController::class, 'showCheckIn'])->name('attendance.checkin');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin.post');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->middleware('role:master')->name('attendance.report');
    Route::patch('/attendance/{id}/approval', [AttendanceController::class, 'updateApproval'])->middleware('role:master')->name('attendance.update.approval');

    // Overtime Requests
    Route::get('/overtime', [App\Http\Controllers\OvertimeController::class, 'index'])->name('overtime.index');
    Route::get('/overtime/create', [App\Http\Controllers\OvertimeController::class, 'create'])->name('overtime.create');
    Route::post('/overtime', [App\Http\Controllers\OvertimeController::class, 'store'])->name('overtime.store');
    Route::get('/overtime/{overtime}', [App\Http\Controllers\OvertimeController::class, 'show'])->name('overtime.show');
    Route::patch('/overtime/{overtime}/approve', [App\Http\Controllers\OvertimeController::class, 'approve'])->middleware('role:master')->name('overtime.approve');
});
