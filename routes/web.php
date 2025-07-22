<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'showLoginForm'])->name('index');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['namespace' => '', 'prefix' => 'admin',  'middleware' => ['auth', 'admin']], function () {
        Route::get('dashboard', [AdminController::class, 'adminGate'])->name('admin.dashboard');
        Route::get('location', [LocationController::class, 'index'])->name('admin.location');
});


Route::group(['namespace' => '', 'prefix' => 'employee',  'middleware' => ['auth', 'employee']], function () {
        Route::get('dashboard', [EmployeeController::class, 'employeeGate'])->name('employee.dashboard');
});
