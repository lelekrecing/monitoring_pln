<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\RegisterController; // <-- TELAH DIHAPUS (Sistem Tertutup)
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InputDataController;
use App\Http\Controllers\Admin\UploadController;
use App\Http\Controllers\StandarKonstruksiController;
use App\Http\Controllers\Admin\StandarKonstruksiController as AdminStandarKonstruksiController;
use App\Http\Controllers\AnggotaController; // <-- TAMBAHAN: Controller Kelola Anggota
use App\Http\Controllers\Admin\HistoryController;


// ==========================
// LOGIN SYSTEM
// ==========================
Route::get('/', function () {
    return view('login');
});

Route::get('/login', function () {
    return view('login');
})->name('login.view');

Route::post('/login', [LoginController::class, 'login'])->name('login');

// ==========================
// LOGOUT (User & Admin)
// ==========================
Route::get('/logout-admin', [AdminAuthController::class, 'logout'])->name('logout.admin');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// ==========================
// DASHBOARD
// ==========================
Route::get('/dashboard', [DashboardController::class, 'userIndex'])->name('dashboard');
Route::get('/dashboard-admin', [DashboardController::class, 'adminIndex'])->name('admin.dashboard');

// ==========================
// KELOLA ANGGOTA (ADMIN ONLY)
// ==========================
Route::prefix('admin/anggota')->name('admin.anggota.')->group(function () {
    Route::get('/', [AnggotaController::class, 'index'])->name('index');
    Route::post('/store', [AnggotaController::class, 'store'])->name('store');
    Route::put('/{id}/update', [AnggotaController::class, 'update'])->name('update');
    Route::delete('/{id}/destroy', [AnggotaController::class, 'destroy'])->name('destroy');
});

// ==========================
// INPUT DATA ADMIN & CHUNKING
// ==========================
Route::get('/input-data', function () {
    return view('input-data');
})->name('input-data');

Route::post('/input-data', [InputDataController::class, 'storeKhs'])->name('input-data.store');
Route::post('/input-data/process-sheet', [InputDataController::class, 'processSheet'])->name('input-data.process-sheet');
Route::post('/input-data/finalize', [InputDataController::class, 'finalizeImport'])->name('input-data.finalize');
Route::post('/input-data/cancel', [InputDataController::class, 'cancelImport'])->name('input-data.cancel');
Route::get('/input-data/progress/{upload}', [InputDataController::class, 'progress'])->name('input-data.progress');

// ==========================
// UPLOAD EXCEL (TIANG & PELANGGAN)
// ==========================
Route::get('/upload-excel', [UploadController::class, 'index'])->name('upload.index');
Route::post('/upload-excel', [InputDataController::class,'storeKhs'])->name('upload.store');
Route::post('/input-data/tiang', [InputDataController::class, 'storeTiang'])->name('input-data.store.tiang');
Route::post('/input-data/pelanggan', [InputDataController::class, 'storePelanggan'])->name('input-data.store.pelanggan');


Route::get('/riwayat-upload', [HistoryController::class, 'index'])->name('history.index');
Route::get('/riwayat-upload/{uploadId}', [HistoryController::class, 'show'])->name('history.show');
Route::post('/riwayat-upload/{uploadId}/aktifkan', [HistoryController::class, 'activate'])->name('history.activate');
Route::delete('/riwayat-upload/{uploadId}', [HistoryController::class, 'destroy'])->name('history.destroy');

// ==========================
// UPDATE DATA & TAMPILAN SHEET
// ==========================
Route::get('/update-data', function () {
    return view('update-data');
});

Route::get('/rekap', function () {
    return view('rekap');
});

Route::get('/sheet/{id}', [DashboardController::class, 'show'])->name('sheet.show');
Route::get('/search-sheet', [DashboardController::class, 'searchSheet'])->name('search.sheet');

// ==========================
// STANDAR KONSTRUKSI (USER - READ ONLY)
// ==========================
Route::get('/standar-konstruksi', [StandarKonstruksiController::class, 'index'])->name('standar-konstruksi.index');

// ==========================
// STANDAR KONSTRUKSI (ADMIN)
// ==========================
Route::prefix('admin/standar-konstruksi')->name('admin.standar-konstruksi.')->group(function () {
    Route::get('/', [AdminStandarKonstruksiController::class, 'index'])->name('index');
    Route::get('/create', [AdminStandarKonstruksiController::class, 'create'])->name('create');
    Route::post('/', [AdminStandarKonstruksiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [AdminStandarKonstruksiController::class, 'edit'])->name('edit');
    Route::put('/{id}', [AdminStandarKonstruksiController::class, 'update'])->name('update');
    Route::delete('/{id}', [AdminStandarKonstruksiController::class, 'destroy'])->name('destroy');
    Route::post('/import', [AdminStandarKonstruksiController::class, 'import'])->name('import');
});