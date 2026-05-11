<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RequestManagementController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\Staff\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/permohonan', [RequestController::class, 'create'])->name('requests.create');
    Route::post('/permohonan', [RequestController::class, 'store'])->name('requests.store');
    Route::get('/permohonan-saya', [RequestController::class, 'index'])->name('requests.index');
    Route::get('/permohonan/{assetRequest}/edit', [RequestController::class, 'edit'])->name('requests.edit');
    Route::put('/permohonan/{assetRequest}', [RequestController::class, 'update'])->name('requests.update');
    Route::get('/laporan-cetak', [RequestController::class, 'print'])->name('requests.print');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/permohonan', [RequestManagementController::class, 'index'])->name('requests.index');
    Route::get('/permohonan/{assetRequest}', [RequestManagementController::class, 'show'])->name('requests.show');
    Route::delete('/permohonan/{assetRequest}', [RequestManagementController::class, 'destroy'])->name('requests.destroy');
    Route::get('/permohonan-cetak', [RequestManagementController::class, 'print'])->name('requests.print');
    Route::get('/permohonan-excel', [RequestManagementController::class, 'exportExcel'])->name('requests.excel');
    Route::patch('/permohonan/item/{requestItem}/status', [RequestManagementController::class, 'updateStatus'])->name('requests.status');
    Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
    Route::get('/pengguna/tambah', [UserController::class, 'create'])->name('users.create');
    Route::post('/pengguna', [UserController::class, 'store'])->name('users.store');
    Route::get('/pengguna/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/pengguna/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware('auth')->get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::middleware('auth')->get('/quotation/{requestItem}', [QuotationController::class, 'show'])->name('quotations.show');
