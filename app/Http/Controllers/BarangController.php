<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        // Mengambil data dari View Database v_barang
        $barang = DB::table('v_barang')->get();
        return view('barang', compact('barang'));
    }

    public function create()
    {
        return view('tambah_barang');
    }

   public function store(Request $request)
    {
        // 1. Ambil data dari formulir
        $nama_barang = $request->input('nama_barang');
        $harga = $request->input('harga');
        $status_barang = $request->input('status_barang');

        // Catatan: input 'stok' dari form tidak kita insert ke tabel barang,
        // karena memang tidak ada kolom stok di tabel tersebut.

        // 2. Simpan ke tabel 'barang' persis seperti kolom di TablePlus
        DB::table('barang')->insert([
            'nama' => $nama_barang,       // Di DB namanya 'nama', bukan 'nama_barang'
            'harga' => $harga,
            'status' => ($status_barang == 'Tersedia') ? '1' : '0',
            // DUA KOLOM DI BAWAH INI WAJIB DIISI KARENA DI DB TIDAK BOLEH KOSONG
            'jenis' => 'S',               // Default jenis Sepatu (S)
            'idsatuan' => 1               // Default idsatuan = 1
        ]);

        return redirect('/barang');
    }
    // Fungsi untuk memunculkan halaman Edit
    public function edit(string $id)
    {
        // Ambil data sepatu berdasarkan idbarang
        $barang = DB::table('barang')->where('idbarang', $id)->first();
        return view('edit_barang', compact('barang'));
    }

    public function update(Request $request, string $id)
{
    // Ambil data yang diketik di form edit
    $nama_barang = $request->input('nama_barang');
    $harga = $request->input('harga');
    $stok = $request->input('stok'); // <-- INI YANG KURANG (Menangkap input stok)
    $status_barang = $request->input('status_barang');

    // Update data di database zuandb
    DB::table('barang')->where('idbarang', $id)->update([
        'nama' => $nama_barang,
        'harga' => $harga,
        'stok' => $stok, // <-- INI JUGA DITAMBAHKAN (Menyimpan stok ke database)
        'status' => ($status_barang == 'Tersedia') ? '1' : '0',
    ]);

    return redirect('/barang');
}

    // Fungsi untuk Menghapus data
    public function hapus(string $id)
    {
        // Menghapus data dari database berdasarkan idbarang
        DB::table('barang')->where('idbarang', $id)->delete();

        // Kembali ke halaman utama setelah berhasil dihapus
        return redirect('/barang');
    }
    public function pengadaan()
    {
        return view('pengadaan');
    }

    public function penerimaan()
    {
        return view('penerimaan');
    }
    public function tampilkanLogin()
    {
        return view('login');
    }

    public function prosesLogin(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Mengubah pencarian dari kolom 'email' menjadi 'username'
        // (atau sesuaikan dengan nama kolom teks pengenal di tabel user kamu)
        $user = DB::table('user')->where('username', $email)->first();

        if ($user && $user->password == $password) {
            return redirect('/barang');
        } else {
            return redirect('/login')->with('error', 'Username atau Password kamu salah, Zuan!');
        }
    }

    public function logout()
    {
        return redirect('/login');
    }
}
