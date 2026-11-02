<?php

/*
 * Pengaturan aturan bisnis inventori.
 * Kode status berupa CHAR(1) di database -- sesuaikan di sini jika konvensi
 * di database kamu berbeda, tanpa perlu mengubah controller.
 */
return [
    // PPN (persen) yang dihitung otomatis pada transaksi pengadaan.
    'ppn_persen' => 11,

    // pengadaan.status
    'status_pengadaan_baru'    => 'P',
    'status_pengadaan_selesai' => 'S',

    // penerimaan.status
    'status_penerimaan' => 'S',

    // kartu_stok.jenis_transaksi untuk barang masuk dari penerimaan
    'kartu_stok_penerimaan' => 'M',
];
