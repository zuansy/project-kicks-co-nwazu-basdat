<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\Master\MarginController;
use App\Http\Controllers\Master\MasterHubController;
use App\Http\Controllers\Master\PenggunaController;
use App\Http\Controllers\Master\RoleController;
use App\Http\Controllers\Master\SatuanController;
use App\Http\Controllers\Master\VendorController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengadaanController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\ReturController;
use App\Http\Controllers\TokoController;
use Illuminate\Support\Facades\Route;

// --- Publik: siapa saja boleh melihat etalase ---
Route::get('/', [TokoController::class, 'index'])->name('toko');

// --- Login & pendaftaran (email + password) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
Route::get('/daftar', [AuthController::class, 'showDaftar'])->name('daftar');
Route::post('/daftar', [AuthController::class, 'daftar'])->name('daftar.proses');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('login')->name('logout');

// --- Panel admin: khusus karyawan ---
Route::middleware(['login', 'staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('barang', BarangController::class)->except('show');

    // Proses bisnis berurutan: pengadaan -> penerimaan (-> retur) -> penjualan
    Route::resource('pengadaan', PengadaanController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('penerimaan', PenerimaanController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('retur', ReturController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('penjualan', PenjualanController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('/kartu-stok', [KartuStokController::class, 'index'])->name('kartustok.index');

    // Data master
    Route::prefix('master')->group(function () {
        Route::get('/', [MasterHubController::class, 'index'])->name('master.hub');

        Route::resource('satuan', SatuanController::class)->except('show');
        Route::resource('vendor', VendorController::class)->except('show');

        Route::middleware('peran:Administrator,Manajer Penjualan,Pemilik')->group(function () {
            Route::resource('margin', MarginController::class)->except('show');
            Route::post('margin/{id}/aktifkan', [MarginController::class, 'aktifkan'])->name('margin.aktifkan');
        });

        Route::middleware('peran:Administrator')->group(function () {
            Route::resource('role', RoleController::class)->except('show');
            Route::resource('pengguna', PenggunaController::class)->except('show');
        });
    });
});
