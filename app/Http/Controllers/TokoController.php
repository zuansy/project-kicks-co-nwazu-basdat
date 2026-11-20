<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Etalase publik: bisa dilihat siapa saja, tanpa login. */
/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  FN 1  fn_cek_sisa_stok   (stok tiap barang)
 *  FN 4  fn_harga_jual      (harga jual = harga dasar + margin aktif)
 */
class TokoController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $jenis = (string) $request->query('jenis', '');

        $produk = DB::table('barang as b')
            ->leftJoin('satuan as s', 's.idsatuan', '=', 'b.idsatuan')
            ->where('b.status', 1)
            ->when($q !== '', fn ($x) => $x->where('b.nama', 'like', "%{$q}%"))
            ->when(array_key_exists($jenis, Barang::JENIS), fn ($x) => $x->where('b.jenis', $jenis))
            ->select(
                'b.idbarang',
                'b.nama',
                'b.jenis',
                DB::raw('fn_harga_jual(b.idbarang) as harga'), // FN 4: fn_harga_jual — harga dasar + margin aktif
                's.nama_satuan',
                DB::raw('fn_cek_sisa_stok(b.idbarang) as stok') // FN 1: fn_cek_sisa_stok — sisa stok
            )
            ->orderBy('b.nama')
            ->paginate(12)
            ->withQueryString();

        return view('toko.index', [
            'produk' => $produk,
            'q'      => $q,
            'jenis'  => $jenis,
            'daftarJenis' => Barang::JENIS,
        ]);
    }
}
