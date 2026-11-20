<?php

namespace App\Http\Controllers;

use App\Support\Sp;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  SP 10 sp_buat_penjualan          (header penjualan)
 *  SP 11 sp_tambah_detail_penjualan  (detail penjualan)
 *  FN 1  fn_cek_sisa_stok            (stok tersedia)
 *  FN 4  fn_harga_jual               (harga satuan penjualan)
 *  TG 2  trg_after_insert_detail_penjualan (otomatis: stok berkurang)
 */
class PenjualanController extends Controller
{
    public function index()
    {
        $penjualan = DB::table('penjualan as j')
            ->join('user as u', 'u.iduser', '=', 'j.iduser')
            ->join('margin_penjualan as m', 'm.idmargin_penjualan', '=', 'j.idmargin_penjualan')
            ->select('j.idpenjualan', 'j.created_at as waktu', 'j.total_nilai', 'u.username', 'm.persen')
            ->orderByDesc('j.idpenjualan')
            ->paginate(10);

        return view('penjualan.index', compact('penjualan'));
    }

    public function create()
    {
        return view('penjualan.create', [
            // FN 4: fn_harga_jual — harga dasar + margin aktif
            // FN 1: fn_cek_sisa_stok — stok tersedia
            'barang' => DB::table('barang as b')
                ->where('b.status', 1)
                ->whereRaw('fn_cek_sisa_stok(b.idbarang) > 0')
                ->select('b.idbarang', 'b.nama',
                    DB::raw('fn_harga_jual(b.idbarang) as harga'),
                    DB::raw('fn_cek_sisa_stok(b.idbarang) as stok'))
                ->orderBy('b.nama')
                ->get(),
            'marginAktif' => DB::table('margin_penjualan')->where('status', 1)->value('persen'),
            'ppnPersen'   => (int) config('inventori.ppn_persen'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item'            => ['required', 'array', 'min:1'],
            'item.*.idbarang' => ['required', 'integer', 'exists:barang,idbarang', 'distinct'],
            'item.*.jumlah'   => ['required', 'integer', 'min:1'],
        ]);

        $idUser = $request->session()->get('iduser');

        try {
            $id = DB::transaction(function () use ($data, $idUser) {
                // SP 10: sp_buat_penjualan — header penjualan; otomatis menarik margin yang aktif
                DB::statement('CALL sp_buat_penjualan(?, @id_baru)', [$idUser]);
                $id = (int) DB::selectOne('SELECT @id_baru AS id')->id;

                // SP 11: sp_tambah_detail_penjualan — harga lewat FN 4 (fn_harga_jual), cek stok lewat FN 1.
                // TG 2 (otomatis di database): trg_after_insert_detail_penjualan mengurangi stok di kartu_stok.
                foreach ($data['item'] as $item) {
                    DB::statement('CALL sp_tambah_detail_penjualan(?, ?, ?)', [
                        $id, (int) $item['idbarang'], (int) $item['jumlah'],
                    ]);
                }

                return $id;
            });
        } catch (QueryException $e) {
            $pesan = Sp::pesan($e);
            if ($pesan === null) {
                throw $e;
            }

            return back()->withInput()->with('error', $pesan);
        }

        return redirect()->route('penjualan.show', $id)->with('success', 'Penjualan berhasil dicatat. Stok sudah berkurang.');
    }

    public function show(string $id)
    {
        $penjualan = DB::table('penjualan as j')
            ->join('user as u', 'u.iduser', '=', 'j.iduser')
            ->join('margin_penjualan as m', 'm.idmargin_penjualan', '=', 'j.idmargin_penjualan')
            ->where('j.idpenjualan', $id)
            ->select('j.idpenjualan', 'j.created_at as waktu', 'j.subtotal_nilai', 'j.ppn', 'j.total_nilai', 'u.username', 'm.persen')
            ->first();

        abort_if($penjualan === null, 404);

        $detail = DB::table('detail_penjualan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.idbarang')
            ->where('d.penjualan_idpenjualan', $id)
            ->select('b.nama', 'd.jumlah', 'd.harga_satuan', 'd.subtotal')
            ->get();

        return view('penjualan.show', compact('penjualan', 'detail'));
    }
}
