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
use App\Models\Attendance;

Route::get('/', [LoginController::class, 'showLoginForm'])->name('index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['namespace' => '', 'prefix' => 'admin',  'middleware' => ['auth', 'admin']], function () {
    Route::get('dashboard', [AdminController::class, 'adminGate'])->name('admin.dashboard');

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

    Route::get('profile', [ProfileController::class, 'profile'])->name('admin.profile.index');
    Route::put('profile/photo/{id}', [ProfileController::class, 'updatePhoto'])->name('admin.profile.upload_photo');
    Route::put('profile/{id}', [ProfileController::class, 'updateData'])->name('admin.profile.updateData');

    Route::get('user-location', [UserLocationController::class, 'index'])->name('admin.userLocation.index');
    Route::get('user-location/{id}/edit', [UserLocationController::class, 'edit'])->name('admin.userLocation.edit');
    Route::put('user-location/{id}', [UserLocationController::class, 'update'])->name('admin.userLocation.update');
    Route::delete('user-location/{id}', [UserLocationController::class, 'destroy'])->name('admin.userLocation.destroy');
});


Route::group(['namespace' => '', 'prefix' => 'employee',  'middleware' => ['auth', 'employee']], function () {
    Route::get('dashboard', [EmployeeController::class, 'employeeGate'])->name('employee.dashboard');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('employee.attendance');
    Route::get('clock_in', [AttendanceController::class, 'index'])->name('employee.clock_in');
    Route::get('clock_out', [AttendanceController::class, 'index'])->name('employee.clock_out');

    Route::get('profile', [EmployeeProfileController::class, 'profile'])->name('employee.profile.index');
    Route::put('profile/photo/{id}', [EmployeeProfileController::class, 'updatePhoto'])->name('employee.profile.upload_photo');
    Route::put('profile/{id}', [EmployeeProfileController::class, 'updateData'])->name('employee.profile.updateData');
});
