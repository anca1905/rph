<?php

use Illuminate\Support\Facades\Route;

// Import semua Controller
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HewanController;
use App\Http\Controllers\IdulAdhaController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\AntemortemController;
use App\Http\Controllers\PemotonganController;
use App\Http\Controllers\PostmortemController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PekerjaIdulAdhaController;
use App\Http\Controllers\UserController;

// ================= ROUTE AUTENTIKASI =================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// RUTE LUPA PASSWORD
Route::get('/lupa-password', [AuthController::class, 'showForgotForm'])->name('password.request');
Route::post('/lupa-password', [AuthController::class, 'sendOtp'])->name('password.email');
Route::get('/verifikasi-otp', [AuthController::class, 'showVerifyForm'])->name('password.verify');
Route::post('/verifikasi-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.submit');
Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// ================= ROUTE SISTEM (HARUS LOGIN) =================
Route::middleware(['auth'])->group(function () {
    
    // --> AKSES UMUM (Semua Role: Admin, Petugas, Pimpinan)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/profil/update', [AuthController::class, 'updateProfile'])->name('profil.update');
    Route::post('/profil/password', [AuthController::class, 'updatePassword'])->name('profil.password');

    // --> KHUSUS PIMPINAN & ADMIN (Hanya Laporan)
    Route::middleware(['role:pimpinan,admin'])->group(function () {
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
        Route::post('/laporan/pengaturan-ttd', [LaporanController::class, 'simpanPengaturanTtd'])->name('laporan.simpan_ttd');
    });

    // --> AKSES MONITORING (Petugas, Admin, Pimpinan, Pekerja Idul Adha)
    Route::middleware(['role:petugas,admin,pimpinan,pekerja_idul_adha'])->group(function () {
        Route::get('/hewan', [HewanController::class, 'index'])->name('hewan.index');
        Route::get('/idul_adha', [IdulAdhaController::class, 'index'])->name('idul_adha.index');
        Route::get('/idul_adha/export/pdf', [IdulAdhaController::class, 'exportPdf'])->name('idul_adha.export_pdf');
        Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
        Route::get('/antemortem', [AntemortemController::class, 'index'])->name('antemortem.index');
        Route::get('/pemotongan', [PemotonganController::class, 'index'])->name('pemotongan.index');
        Route::get('/postmortem', [PostmortemController::class, 'index'])->name('postmortem.index');
    });

    // --> KHUSUS ADMIN (Kelola Pekerja)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/pekerja', [PekerjaIdulAdhaController::class, 'index'])->name('pekerja.index');
        Route::post('/pekerja/store', [PekerjaIdulAdhaController::class, 'store'])->name('pekerja.store');
        Route::put('/pekerja/update/{id}', [PekerjaIdulAdhaController::class, 'update'])->name('pekerja.update');
        Route::delete('/pekerja/destroy/{id}', [PekerjaIdulAdhaController::class, 'destroy'])->name('pekerja.destroy');
    });

    // --> KHUSUS PETUGAS, ADMIN, PEKERJA IDUL ADHA (Aksi Data Idul Adha)
    Route::middleware(['role:petugas,admin,pekerja_idul_adha'])->group(function () {
        // Rute Pemotongan Idul Adha (Standalone)
        Route::post('/idul_adha', [IdulAdhaController::class, 'store'])->name('idul_adha.store');
        Route::post('/idul_adha/import', [IdulAdhaController::class, 'import'])->name('idul_adha.import');
        Route::put('/idul_adha/update/{id}', [IdulAdhaController::class, 'update'])->name('idul_adha.update');
        Route::delete('/idul_adha/delete/{id}', [IdulAdhaController::class, 'destroy'])->name('idul_adha.destroy');
    });

    // --> KHUSUS PETUGAS & ADMIN (Aksi Data Operasional Harian)
    Route::middleware(['role:petugas,admin'])->group(function () {
        
        // Data Hewan
        Route::post('/hewan/store', [HewanController::class, 'store'])->name('hewan.store');
        Route::post('/hewan/import', [HewanController::class, 'import'])->name('hewan.import');
        Route::put('/hewan/update/{id}', [HewanController::class, 'update'])->name('hewan.update');
        Route::delete('/hewan/destroy/{id}', [HewanController::class, 'destroy'])->name('hewan.destroy');

        // Data Pembayaran
        Route::post('/pembayaran/store', [PembayaranController::class, 'store'])->name('pembayaran.store');
        Route::put('/pembayaran/update/{id}', [PembayaranController::class, 'update'])->name('pembayaran.update');
        Route::delete('/pembayaran/destroy/{id}', [PembayaranController::class, 'destroy'])->name('pembayaran.destroy');

        // Antemortem
        Route::post('/antemortem/store', [AntemortemController::class, 'store'])->name('antemortem.store');
        Route::put('/antemortem/update/{id}', [AntemortemController::class, 'update'])->name('antemortem.update');
        Route::put('/antemortem/verifikasi/{id}', [AntemortemController::class, 'verifikasi'])->name('antemortem.verifikasi');
        Route::delete('/antemortem/destroy/{id}', [AntemortemController::class, 'destroy'])->name('antemortem.destroy');

        // Pemotongan
        Route::post('/pemotongan/store', [PemotonganController::class, 'store'])->name('pemotongan.store');
        Route::put('/pemotongan/update/{id}', [PemotonganController::class, 'update'])->name('pemotongan.update');
        Route::delete('/pemotongan/destroy/{id}', [PemotonganController::class, 'destroy'])->name('pemotongan.destroy');

        // Postmortem
        Route::post('/postmortem/store', [PostmortemController::class, 'store'])->name('postmortem.store');
        Route::put('/postmortem/update/{id}', [PostmortemController::class, 'update'])->name('postmortem.update');
        Route::delete('/postmortem/destroy/{id}', [PostmortemController::class, 'destroy'])->name('postmortem.destroy');
    });

});
