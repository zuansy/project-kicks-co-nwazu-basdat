<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuStokController extends Controller
{
    public function index(Request $request)
    {
        $idBarang = $request->query('barang');

        $riwayat = DB::table('kartu_stok as k')
            ->join('barang as b', 'b.idbarang', '=', 'k.idbarang')
            ->when($idBarang, fn ($q) => $q->where('k.idbarang', $idBarang))
            ->select(
                'k.idkartu_stok',
                'k.created_at as waktu',
                'b.nama',
                'k.jenis_transaksi',
                'k.masuk',
                'k.keluar',
                'k.stock',
                'k.idtransaksi'
            )
            ->orderByDesc('k.idkartu_stok')
            ->paginate(15)
            ->withQueryString();

        return view('kartustok.index', [
            'riwayat'  => $riwayat,
            'barang'   => DB::table('barang')->orderBy('nama')->get(['idbarang', 'nama']),
            'idBarang' => $idBarang,
        ]);
    }
}
