<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

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
});


Route::group(['namespace' => '', 'prefix' => 'employee',  'middleware' => ['auth', 'employee']], function () {
    Route::get('dashboard', [EmployeeController::class, 'employeeGate'])->name('employee.dashboard');
});
