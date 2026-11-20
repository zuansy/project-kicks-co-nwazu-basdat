<?php

/*
 * Aturan bisnis inventori.
 * Kode status (CHAR(1)) harus sama dengan isi SP/trigger di
 * database/perubahan-proses-bisnis.sql.
 */
return [
    // PPN (persen) - hanya untuk perkiraan di form; angka resmi dihitung fn_hitung_ppn11 di database.
    'ppn_persen' => 11,

    // Pengadaan: hanya 2 status
    'status_pengadaan_proses'  => 'P',
    'status_pengadaan_selesai' => 'S',

    // Penerimaan: sebagian atau tuntas
    'status_penerimaan_sebagian' => 'P',
    'status_penerimaan_tuntas'   => 'T',

    // Minimal jumlah jenis barang dalam satu pengadaan ("barang pengadaan tidak boleh satu")
    'min_barang_pengadaan' => 2,

    // Role
    'role_pelanggan' => 'Pelanggan',
    'role_admin'     => ['Administrator'],
    'role_margin'    => ['Administrator', 'Manajer Penjualan', 'Pemilik'],
];
