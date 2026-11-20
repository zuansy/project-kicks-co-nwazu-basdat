<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  FN 1  fn_cek_sisa_stok   (stok menipis dan total stok)
 */
class DashboardController extends Controller
{
    private const BATAS_MENIPIS = 5;

    public function index()
    {
        // FN 1: fn_cek_sisa_stok — sisa stok tiap barang dari kartu_stok.
        $menipis = DB::table('barang as b')
            ->where('b.status', 1)
            ->whereRaw('fn_cek_sisa_stok(b.idbarang) <= ?', [self::BATAS_MENIPIS])
            ->select('b.idbarang', 'b.nama', DB::raw('fn_cek_sisa_stok(b.idbarang) as stok'))
            ->orderBy('stok')
            ->orderBy('b.nama')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'totalBarang'        => DB::table('barang')->where('status', 1)->count(),
            'totalStok'          => (int) DB::table('barang')->where('status', 1)
                ->selectRaw('COALESCE(SUM(fn_cek_sisa_stok(idbarang)), 0) as total')->value('total'), // FN 1: total stok
            'pengadaanMenunggu'  => DB::table('pengadaan')
                ->where('status', config('inventori.status_pengadaan_proses'))->count(),
            'penjualanBulanIni'  => DB::table('penjualan')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)->count(),
            'menipis'            => $menipis,
            'batas'              => self::BATAS_MENIPIS,
        ]);
    }
}
