<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLocationController;
use App\Http\Controllers\Employee\EmployeeProfileController;
use App\Http\Controllers\Employee\ClockInController;
use App\Http\Controllers\Employee\ClockOutController;
use App\Http\Controllers\Employee\EmployeeAttendance;
use App\Http\Middleware\Employee;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Employee\EmployeeAttendanceController;
use App\Http\Controllers\DashboardController;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['namespace' => '', 'prefix' => 'admin',  'middleware' => ['auth', 'admin']], function () {
    Route::get('dashboardadmin', [AdminController::class, 'adminGate'])->name('admin.dashboard');
    Route::get('dashboard/stats', [AdminController::class, 'adminStats'])->name('admin.dashboard.stats');

    Route::get('dashboard', [AdminController::class, 'index'])->name('admin.index');
    // Route::get('dashboard/stats', function () {
    //     $today = now()->toDateString();
    //     return response()->json([
    //         'totalEmployees'  => \App\Models\User::count(),
    //         'totalLocations'  => \App\Models\Location::count(),
    //         'totalAttendance' => \App\Models\Attendance::count(),
    //         'todayAttendance' => \App\Models\Attendance::whereDate('clock_in_time', $today)
    //             ->distinct('user_id')->count('user_id'),
    //     ]);
    // })->middleware('auth')->name('admin.dashboard.stats');
    // Route::get('dashboard/stats', [DashboardController::class, 'stats'])->name('admin.dashboard.stats');

    Route::get('location', [LocationController::class, 'index'])->name('admin.location');
    Route::get('location/{id}', [LocationController::class, 'index'])->name('admin.location.edit');
    Route::post('locations', [LocationController::class, 'store'])->name('admin.location.store');
    Route::put('locations/{id}', [LocationController::class, 'update'])->name('admin.location.update');
    Route::delete('locations/{id}', [LocationController::class, 'destroy'])->name('admin.location.destroy');

    Route::get('user', [UserController::class, 'index'])->name('admin.user');
    Route::post('user', [UserController::class, 'store'])->name('admin.user.store');
    Route::delete('user/{id}', [UserController::class, 'destroy'])->name('admin.user.destroy');
    Route::put('user/{id}', [UserController::class, 'update'])->name('admin.user.update');
    Route::get('user/{id}', [UserController::class, 'index'])->name('admin.user.edit');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('admin.attendance');
    Route::delete('attendance/delete-by-periode', [AttendanceController::class, 'destroyByPeriode'])->name('admin.attendance.destroyByPeriode');
    Route::delete('attendance/{id}', [AttendanceController::class, 'destroy'])->whereUuid('id')
        ->name('admin.attendance.destroy');
    Route::get('attendance/export-by-periode', [AttendanceController::class, 'exportByPeriode'])->name('admin.attendance.exportByPeriode');

    Route::get('profile', [ProfileController::class, 'profile'])->name('admin.profile.index');
    Route::put('profile/photo/{id}', [ProfileController::class, 'updatePhoto'])->name('admin.profile.upload_photo');
    Route::put('profile/{id}', [ProfileController::class, 'updateData'])->name('admin.profile.updateData');

    Route::get('user-location', [UserLocationController::class, 'index'])->name('admin.userLocation.index');
    Route::post('user-location', [UserLocationController::class, 'store'])->name('admin.userLocation.store');
    Route::get('user-location/{id}/edit', [UserLocationController::class, 'edit'])->name('admin.userLocation.edit');
    Route::put('user-location/{id}', [UserLocationController::class, 'update'])->name('admin.userLocation.update');
    Route::delete('user-location/{id}', [UserLocationController::class, 'destroy'])->name('admin.userLocation.destroy');
});
Route::group(['namespace' => '', 'prefix' => 'employee',  'middleware' => ['auth', 'employee']], function () {
    Route::get('dashboard', [EmployeeController::class, 'employeeGate'])->name('employee.dashboard');

    Route::get('clock/in', [ClockInController::class, 'clockIn'])->name('employee.clock.clockin');
    Route::post('clock/in', [ClockInController::class, 'storeClockIn'])->name('employee.clock.clockin.store');
    Route::get('clock/out', [ClockOutController::class, 'clockOut'])->name('employee.clock.clockout');
    Route::post('clock/out', [ClockOutController::class, 'storeClockOut'])->name('employee.clock.clockout.store');

    Route::get('attendance', [EmployeeAttendanceController::class, 'index'])->name('employee.attendance.index');

    Route::get('profile', [EmployeeProfileController::class, 'profile'])->name('employee.profile.index');
    Route::put('profile/photo/{id}', [EmployeeProfileController::class, 'updatePhoto'])->name('employee.profile.upload_photo');
    Route::put('profile/{id}', [EmployeeProfileController::class, 'updateData'])->name('employee.profile.updateData');
});
