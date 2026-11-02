<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KartuStokController;
use App\Http\Controllers\PenerimaanController;
use App\Http\Controllers\PengadaanController;
use Illuminate\Support\Facades\Route;

// --- Login (tanpa proteksi) ---
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.proses');

// --- Halaman yang wajib login ---
Route::middleware('login')->group(function () {
    Route::redirect('/', '/barang');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('barang', BarangController::class)->except('show');

    Route::resource('pengadaan', PengadaanController::class)
        ->only(['index', 'create', 'store', 'show']);

    Route::resource('penerimaan', PenerimaanController::class)
        ->only(['index', 'create', 'store']);

    Route::get('/kartu-stok', [KartuStokController::class, 'index'])->name('kartustok.index');
});
