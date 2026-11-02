# Catatan Perbaikan project-pdb

## Cara pasang
1. Ekstrak zip ini ke folder `project-pdb` (timpa file yang sama). `.env`, `vendor`, dan `storage` tidak tersentuh.
2. **Hapus file lama yang sudah digantikan** (tidak terpakai lagi):
   - `resources/views/barang.blade.php`
   - `resources/views/tambah_barang.blade.php`
   - `resources/views/edit_barang.blade.php`
   - `resources/views/pengadaan.blade.php`
   - `resources/views/penerimaan.blade.php`
3. Disarankan di `.env`: `CACHE_STORE=file` dan `QUEUE_CONNECTION=sync`
   (tabel `cache`/`jobs` tidak ada di `zuandb`, jadi pengaturan `database` akan error bila dipakai).
4. `php artisan view:clear` lalu `php artisan serve`.

## Bug yang diperbaiki
| Di mana | Masalah | Perbaikan |
|---|---|---|
| `BarangController::index` | Pencarian pakai `'%{$q}%'` (kutip tunggal, variabel tidak terisi) | Kutip ganda + form cari di halaman |
| `BarangController::update` | Menulis kolom `stok` yang tidak ada di tabel `barang` (pasti error) | Dihapus; stok dikelola `kartu_stok` |
| `BarangController::store` | `jenis` & `idsatuan` di-hardcode, input stok diabaikan | Form memilih jenis & satuan, ada validasi |
| Hapus barang | Lewat GET (rentan CSRF), error FK tidak ditangani | `DELETE` + pesan ramah bila barang sudah dipakai transaksi |
| Model `Barang`/`Satuan` | Tidak ada `$timestamps = false`; `stok` di `fillable` | Diperbaiki |
| Login | Tidak ada session, halaman tidak diproteksi, logout kosong | Session + middleware `login`, logout via POST |
| Layout | Path aset relatif (rusak di `/barang/tambah`), menu = halaman demo, pagination tidak tampil | `asset()`, menu Barang/Pengadaan/Penerimaan, Bootstrap pagination |
| `ReadOnlyModel` | Pesan error tanpa spasi | Diperbaiki |

## Fitur yang diselesaikan
- **Barang**: daftar + cari + paginasi, tambah, edit, hapus (tema Acorn, seragam).
- **Pengadaan**: pilih vendor, banyak baris barang, PPN otomatis, tersimpan ke `pengadaan` + `detail_pengadaan`, halaman detail.
- **Penerimaan**: pilih pengadaan, isi jumlah diterima (dicek terhadap sisa pesanan), simpan ke `penerimaan` + `detail_penerimaan`, stok masuk ke `kartu_stok`, pengadaan otomatis "Selesai" bila lengkap.

## Sudah dicocokkan dengan `zuandb.sql`
- Nama semua tabel/kolom yang dipakai sudah dicek terhadap dump.
- `v_barang` tidak punya `stok` -> stok dihitung dari saldo terakhir `kartu_stok`.
- `v_satuan.status_satuan` bernilai teks `'Aktif'` (bukan 1) -> filter dropdown satuan diperbaiki
  (kode lama akan menghasilkan dropdown kosong).
- Vendor yang bisa dipilih hanya yang `status = '1'`.
- Password di tabel `user` berupa teks biasa (`hash_pass_...`), login membandingkannya apa adanya.

## Asumsi yang masih perlu kamu cek
- **Kode status/jenis_transaksi** (`P`, `S`, `M`) adalah tebakan karena dump tidak berisi data transaksi; ubah di `config/inventori.php`.
- **PPN** disimpan sebagai nominal rupiah (11%), `total = subtotal + ppn`.
- **Trigger**: dump belum punya trigger. Jika kamu membuat trigger INSERT pada `detail_penerimaan`
  (Modul 10), aplikasi otomatis TIDAK menulis `kartu_stok` sendiri agar tidak dobel.

## Belum dikerjakan
Penjualan, retur (kondisi barang cacat), margin penjualan, dan manajemen vendor/satuan/user.
