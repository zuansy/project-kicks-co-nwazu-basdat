# Daftar SP, Function, dan Trigger

Nomor di sini sama dengan label di kode (`// SP 3`, `// FN 2`, `// TG 1`, ...).
Di VS Code tekan **Ctrl+Shift+F** lalu cari mis. `SP 6` untuk melihat semua tempat yang memakainya.

Dipasang dengan `php artisan db:pasang-proses-bisnis` (isi di `perubahan-proses-bisnis.sql`).

## Function (4)

| No | Nama | Tugas | Dipakai di |
|---|---|---|---|
| FN 1 | `fn_cek_sisa_stok(idbarang)` | stok = total masuk - total keluar dari `kartu_stok` | BarangController, DashboardController, TokoController, PenjualanController |
| FN 2 | `fn_hitung_ppn11(subtotal)` | PPN 11% dari subtotal | di dalam SP 4 dan SP 11 |
| FN 3 | `fn_sisa_pesanan(idpengadaan, idbarang)` | sisa belum diterima = dipesan - diterima + diretur | PengadaanController, PenerimaanController, di dalam SP 6 dan SP 7 |
| FN 4 | `fn_harga_jual(idbarang)` | harga dasar + margin yang aktif | TokoController, PenjualanController, di dalam SP 11 |

## Stored Procedure (12)

| No | Nama | Tugas | Dipanggil dari |
|---|---|---|---|
| SP 1 | `sp_tambah_barang` | tambah barang (aktif, stok 0) | BarangController@store |
| SP 2 | `sp_nonaktifkan_barang` | nonaktifkan barang (soft delete) | BarangController@destroy |
| SP 3 | `sp_buat_pengadaan` | header pengadaan, status P (proses) | PengadaanController@store |
| SP 4 | `sp_tambah_detail_pengadaan` | detail pengadaan; PPN lewat FN 2; total dihitung ulang | PengadaanController@store |
| SP 5 | `sp_buat_penerimaan` | header penerimaan (hanya pengadaan berstatus proses) | PenerimaanController@store |
| SP 6 | `sp_tambah_detail_penerimaan` | detail penerimaan; jumlah dan harga terima manual; cek sisa lewat FN 3 | PenerimaanController@store |
| SP 7 | `sp_tutup_penerimaan` | status penerimaan tuntas/sebagian; bila tuntas pengadaan selesai | PenerimaanController@store |
| SP 8 | `sp_buat_retur` | header retur | ReturController@store |
| SP 9 | `sp_tambah_detail_retur` | detail retur; cek jumlah, alasan, stok | ReturController@store |
| SP 10 | `sp_buat_penjualan` | header penjualan; menarik margin aktif | PenjualanController@store |
| SP 11 | `sp_tambah_detail_penjualan` | detail penjualan; harga lewat FN 4; cek stok lewat FN 1 | PenjualanController@store |
| SP 12 | `sp_aktifkan_margin` | hanya satu margin yang boleh aktif | Master/MarginController |

## Trigger (3) - jalan otomatis di database, tidak dipanggil dari PHP

| No | Nama | Kapan jalan | Efeknya |
|---|---|---|---|
| TG 1 | `trg_after_insert_detail_penerimaan` | setelah baris `detail_penerimaan` masuk (oleh SP 6) | stok masuk ke `kartu_stok` (jenis M) |
| TG 2 | `trg_after_insert_detail_penjualan` | setelah baris `detail_penjualan` masuk (oleh SP 11) | stok berkurang di `kartu_stok` (jenis J) |
| TG 3 | `trg_after_insert_detail_retur` | setelah baris `detail_retur` masuk (oleh SP 9) | stok berkurang (jenis R); pengadaan selesai kembali jadi proses bila ada kekurangan |

## Alur: siapa memicu siapa

- **Tambah barang** -> SP 1
- **Pengadaan** -> SP 3 (header) -> SP 4 (tiap barang, di dalamnya FN 2)
- **Penerimaan** -> SP 5 (header) -> SP 6 (tiap barang, FN 3) -> **TG 1** (stok masuk) -> SP 7 (status; pengadaan selesai bila lengkap)
- **Retur** -> SP 8 (header) -> SP 9 (tiap barang) -> **TG 3** (stok berkurang)
- **Penjualan** -> SP 10 (header, margin aktif) -> SP 11 (tiap barang, FN 4 + FN 1) -> **TG 2** (stok berkurang)
- **Margin** -> SP 12
