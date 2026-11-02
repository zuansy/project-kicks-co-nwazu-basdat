<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaan = DB::table('pengadaan as p')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'p.user_iduser')
            ->select(
                'p.idpengadaan',
                'p.timestamp as waktu',
                'p.status',
                'p.total_nilai',
                'v.nama_vendor',
                'u.username'
            )
            ->orderByDesc('p.idpengadaan')
            ->paginate(10);

        return view('pengadaan.index', compact('pengadaan'));
    }

    public function create()
    {
        return view('pengadaan.create', [
            'vendor'    => DB::table('vendor')->where('status', '1')->orderBy('nama_vendor')->get(),
            'barang'    => DB::table('barang')->where('status', 1)->orderBy('nama')->get(['idbarang', 'nama']),
            'ppnPersen' => (int) config('inventori.ppn_persen'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_idvendor'  => ['required', 'integer', Rule::exists('vendor', 'idvendor')->where('status', '1')],
            'item'             => ['required', 'array', 'min:1'],
            'item.*.idbarang'  => ['required', 'integer', 'exists:barang,idbarang', 'distinct'],
            'item.*.jumlah'    => ['required', 'integer', 'min:1'],
            'item.*.harga'     => ['required', 'integer', 'min:0'],
        ]);

        $subtotal = 0;
        $baris = [];
        foreach ($data['item'] as $item) {
            $sub = (int) $item['jumlah'] * (int) $item['harga'];
            $subtotal += $sub;
            $baris[] = [
                'idbarang'     => (int) $item['idbarang'],
                'jumlah'       => (int) $item['jumlah'],
                'harga_satuan' => (int) $item['harga'],
                'sub_total'    => $sub,
            ];
        }

        $ppn = (int) round($subtotal * (int) config('inventori.ppn_persen') / 100);
        $idUser = $request->session()->get('iduser');

        $id = DB::transaction(function () use ($data, $baris, $subtotal, $ppn, $idUser) {
            $idPengadaan = DB::table('pengadaan')->insertGetId([
                'timestamp'       => now(),
                'user_iduser'     => $idUser,
                'status'          => config('inventori.status_pengadaan_baru'),
                'vendor_idvendor' => $data['vendor_idvendor'],
                'subtotal_nilai'  => $subtotal,
                'ppn'             => $ppn,
                'total_nilai'     => $subtotal + $ppn,
            ]);

            DB::table('detail_pengadaan')->insert(
                array_map(fn ($b) => $b + ['idpengadaan' => $idPengadaan], $baris)
            );

            return $idPengadaan;
        });

        return redirect()->route('pengadaan.show', $id)
            ->with('success', 'Pengadaan berhasil dicatat.');
    }

    public function show(string $id)
    {
        $pengadaan = DB::table('pengadaan as p')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'p.user_iduser')
            ->where('p.idpengadaan', $id)
            ->select(
                'p.idpengadaan',
                'p.timestamp as waktu',
                'p.status',
                'p.subtotal_nilai',
                'p.ppn',
                'p.total_nilai',
                'v.nama_vendor',
                'u.username'
            )
            ->first();

        abort_if($pengadaan === null, 404);

        $detail = DB::table('detail_pengadaan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.idbarang')
            ->where('d.idpengadaan', $id)
            ->select('b.nama', 'd.jumlah', 'd.harga_satuan', 'd.sub_total')
            ->get();

        return view('pengadaan.show', [
            'pengadaan' => $pengadaan,
            'detail'    => $detail,
            'selesai'   => $pengadaan->status === config('inventori.status_pengadaan_selesai'),
        ]);
    }
}
