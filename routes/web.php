<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
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
    Route::put('user/{id} ', [UserController::class, 'update'])->name('admin.user.update');
    Route::get('user/{id}', [UserController::class, 'index'])->name('admin.user.edit');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('admin.attendance');
    // Route::get('profile', [UserController::class, 'profile'])->name('admin.profile');
    // Route::post('profile', [PhotoController::class, 'store'])->name('admin.profile.store');
    // Route::put('profile/{id}', [PhotoController::class, 'update'])->name('admin.profile.update');
    // Route::delete('profile/{id}/photo', [PhotoController::class, 'destroy'])->name('admin.profile.destroy');
    // Route::put('profile/{user}/photo', [PhotoController::class, 'updatePhoto'])->name('admin.profile.upload_photo');
    // Route::put('profile/{user}', [PhotoController::class, 'update'])->name('admin.profile.update');
    // Route::get('profile/{user}', [UserController::class, 'show'])->name('admin.profile.show');
    // Route::put('profile/{id}/upload-photo', [PhotoController::class, 'uploadPhoto'])->name('admin.profile.upload_photo');
    // Route::delete('profile/photo/{id}', [PhotoController::class, 'destroy'])->name('admin.profile.delete_photo');

    Route::get('profile', [ProfileController::class, 'profile'])->name('admin.profile.index');
    Route::put('profile/photo/{id}', [ProfileController::class, 'updatePhoto'])->name('admin.profile.upload_photo');
    Route::put('profile/{id}', [ProfileController::class, 'updateData'])->name('admin.profile.updateData');
});


Route::group(['namespace' => '', 'prefix' => 'employee',  'middleware' => ['auth', 'employee']], function () {
    Route::get('dashboard', [EmployeeController::class, 'employeeGate'])->name('employee.dashboard');
    Route::get('attendance', [AttendanceController::class, 'index'])->name('employee.attendance');
    Route::get('clock_in', [AttendanceController::class, 'index'])->name('employee.clock_in');
    Route::get('clock_out', [AttendanceController::class, 'index'])->name('employee.clock_out');
});
