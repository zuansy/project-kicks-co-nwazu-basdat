<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangController;
Route::get('/', function () {
    return view('welcome');
});


Route::get('/barang', [BarangController::class, 'index']);
Route::get('/barang/tambah', [BarangController::class, 'create']); // Tambahkan baris ini
Route::post('/barang/simpan', [BarangController::class, 'store']);
Route::get('/barang/edit/{id}', [BarangController::class, 'edit']);
Route::get('/barang/hapus/{id}', [BarangController::class, 'hapus']);
Route::post('/barang/update/{id}', [BarangController::class, 'update']);
Route::get('/pengadaan', [BarangController::class, 'pengadaan']);
Route::get('/penerimaan', [BarangController::class, 'penerimaan']);
Route::get('/login', [BarangController::class, 'tampilkanLogin']);
Route::post('/login/proses', [BarangController::class, 'prosesLogin']);
Route::get('/logout', [BarangController::class, 'logout']);
