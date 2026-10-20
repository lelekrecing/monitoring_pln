<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\StandarKonstruksiController;
use App\Http\Controllers\Admin\StandarKonstruksiController as AdminStandarKonstruksiController;
use App\Http\Middleware\AdminSession;
use App\Http\Middleware\UserArea;
use App\Http\Controllers\Admin\AnggotaController;
use App\Http\Controllers\Admin\RiwayatUploadController;

// ==========================
// LOGIN USER
// ==========================
Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login.view');

Route::post('/login', [LoginController::class, 'login'])->name('login');

// ==========================
// LOGIN ADMIN
// ==========================
Route::get('/admin', function () {
    return view('login_admin');
})->name('admin.login.view');

Route::post('/admin/login', [LoginController::class, 'adminLogin'])->name('admin.login');

Route::get('/logout-admin', [AdminAuthController::class, 'logout'])->name('logout.admin');

// Logout User
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ==========================
// REGISTRASI
// ==========================
Route::get('/registrasi', function () {
    return view('registrasi');
});

Route::post('/registrasi', [RegisterController::class, 'store'])->name('register.store');

// ==========================
// DASHBOARD
// ==========================
// Dashboard USER: kalau yang buka adalah admin -> dialihkan ke dashboard admin
Route::get('/dashboard', [DashboardController::class, 'userIndex'])
    ->middleware(UserArea::class)
    ->name('dashboard');

// Dashboard ADMIN: wajib login admin
Route::get('/dashboard-admin', [DashboardController::class, 'adminIndex'])
    ->middleware(AdminSession::class)
    ->name('admin.dashboard');

// ==========================
// INPUT DATA ADMIN
// ==========================
Route::get('/input-data', function () {
    return view('input-data');
})->name('input-data');

Route::post('/input-data', [InputDataController::class, 'storeKhs'])->name('input-data.store');

// ==========================
// UPLOAD EXCEL
// ==========================
Route::get('/upload-excel', [UploadController::class, 'index'])->name('upload.index');

Route::post('/upload-excel', [InputDataController::class, 'storeKhs'])
    ->name('upload.store');

// Upload terpisah - Monitoring Tiang & Monitoring Pelanggan
Route::post('/input-data/tiang', [InputDataController::class, 'storeTiang'])
    ->name('input-data.store.tiang');

Route::post('/input-data/pelanggan', [InputDataController::class, 'storePelanggan'])
    ->name('input-data.store.pelanggan');

// ==========================
// UPDATE DATA
// ==========================
Route::get('/update-data', function () {
    return view('update-data');
});

Route::get('/rekap', function () {
    return view('rekap');
});

Route::get('/sheet/{id}', [DashboardController::class, 'show'])
    ->name('sheet.show');

// Live Search (Find/Search) Daftar Sheet
Route::get('/search-sheet', [DashboardController::class, 'searchSheet'])
    ->name('search.sheet');

// ==========================
// STANDAR KONSTRUKSI (Katalog/Galeri) - USER (read-only)
// ==========================
Route::get('/standar-konstruksi', [StandarKonstruksiController::class, 'index'])
    ->name('standar-konstruksi.index');

// ==========================
// STANDAR KONSTRUKSI - ADMIN (dilindungi middleware AdminSession)
// ==========================
Route::prefix('admin/standar-konstruksi')
    ->middleware(AdminSession::class)
    ->name('admin.standar-konstruksi.')
    ->group(function () {
        Route::get('/', [AdminStandarKonstruksiController::class, 'index'])->name('index');
        Route::get('/create', [AdminStandarKonstruksiController::class, 'create'])->name('create');
        Route::post('/', [AdminStandarKonstruksiController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminStandarKonstruksiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminStandarKonstruksiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminStandarKonstruksiController::class, 'destroy'])->name('destroy');

        // Import Excel/Spreadsheet "Standar Kontruksi"
        Route::post('/import', [AdminStandarKonstruksiController::class, 'import'])->name('import');
    });
  
// ==========================
// MANAJEMEN AKUN (ANGGOTA) - ADMIN (dilindungi middleware AdminSession)
// ==========================
Route::prefix('admin/anggota')
    ->middleware(AdminSession::class)
    ->name('admin.anggota.')
    ->group(function () {
        Route::get('/', [AnggotaController::class, 'index'])->name('index');
        Route::put('/{id}/reset-password', [AnggotaController::class, 'resetPassword'])->name('reset-password');
        Route::delete('/{id}', [AnggotaController::class, 'destroy'])->name('destroy');
    });
// / 2) Tambahkan di bagian bawah routes/web.php:
 
// ==========================
// RIWAYAT UPLOAD - ADMIN
// ==========================
Route::middleware(AdminSession::class)->group(function () {
    Route::get('/riwayat-upload', [RiwayatUploadController::class, 'index'])->name('admin.riwayat-upload');
    Route::post('/riwayat-upload/{id}/aktifkan', [RiwayatUploadController::class, 'aktifkan'])->name('admin.riwayat-upload.aktifkan');
    Route::delete('/riwayat-upload/{id}', [RiwayatUploadController::class, 'destroy'])->name('admin.riwayat-upload.destroy');
});