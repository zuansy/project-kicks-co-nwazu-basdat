<?php

namespace App\Http\Controllers;

use App\Support\Sp;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  SP 3  sp_buat_pengadaan          (header pengadaan)
 *  SP 4  sp_tambah_detail_pengadaan  (detail pengadaan, hitung PPN dan total)
 *  FN 2  fn_hitung_ppn11             (dipanggil di dalam SP 4)
 *  FN 3  fn_sisa_pesanan             (kolom sisa di halaman detail)
 */
class PengadaanController extends Controller
{
    public function index()
    {
        $pengadaan = DB::table('pengadaan as p')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'p.user_iduser')
            ->select('p.idpengadaan', 'p.timestamp as waktu', 'p.status', 'p.total_nilai', 'v.nama_vendor', 'u.username')
            ->orderByDesc('p.idpengadaan')
            ->paginate(10);

        return view('pengadaan.index', compact('pengadaan'));
    }

    public function create()
    {
        return view('pengadaan.create', [
            'vendor'    => DB::table('vendor')->where('status', '1')->orderBy('nama_vendor')->get(),
            // harga dasar barang dipakai sebagai harga awal; boleh diubah manual
            'barang'    => DB::table('barang')->where('status', 1)->orderBy('nama')->get(['idbarang', 'nama', 'harga']),
            'ppnPersen' => (int) config('inventori.ppn_persen'),
            'minBarang' => (int) config('inventori.min_barang_pengadaan'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'vendor_idvendor' => ['required', 'integer', Rule::exists('vendor', 'idvendor')->where('status', '1')],
            'item'            => ['required', 'array', 'min:'.(int) config('inventori.min_barang_pengadaan')],
            'item.*.idbarang' => ['required', 'integer', 'exists:barang,idbarang', 'distinct'],
            'item.*.jumlah'   => ['required', 'integer', 'min:1'],
            'item.*.harga'    => ['required', 'integer', 'min:0'],
        ]);

        $idUser = $request->session()->get('iduser');

        try {
            $id = DB::transaction(function () use ($data, $idUser) {
                // SP 3: sp_buat_pengadaan — header pengadaan baru (status P = proses). Vendor dipilih manual.
                DB::statement('CALL sp_buat_pengadaan(?, ?, @id_baru)', [$idUser, $data['vendor_idvendor']]);
                $id = (int) DB::selectOne('SELECT @id_baru AS id')->id;

                // SP 4: sp_tambah_detail_pengadaan — satu panggilan per barang; di dalamnya memanggil
                // FN 2: fn_hitung_ppn11 untuk PPN 11% otomatis, lalu total dihitung ulang.
                foreach ($data['item'] as $item) {
                    DB::statement('CALL sp_tambah_detail_pengadaan(?, ?, ?, ?)', [
                        $id, (int) $item['idbarang'], (int) $item['jumlah'], (int) $item['harga'],
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

        return redirect()->route('pengadaan.show', $id)
            ->with('success', 'Pengadaan dicatat dengan status proses. Lanjutkan dengan menerima barangnya.');
    }

    public function show(string $id)
    {
        $pengadaan = DB::table('pengadaan as p')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'p.user_iduser')
            ->where('p.idpengadaan', $id)
            ->select('p.idpengadaan', 'p.timestamp as waktu', 'p.status', 'p.subtotal_nilai', 'p.ppn', 'p.total_nilai', 'v.nama_vendor', 'u.username')
            ->first();

        abort_if($pengadaan === null, 404);

        $detail = DB::table('detail_pengadaan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.idbarang')
            ->where('d.idpengadaan', $id)
            ->select(
                'b.nama', 'd.jumlah', 'd.harga_satuan', 'd.sub_total',
                // FN 3: fn_sisa_pesanan — sisa yang belum diterima (dipesan - diterima + diretur)
                DB::raw('fn_sisa_pesanan(d.idpengadaan, d.idbarang) as sisa')
            )
            ->get();

        $penerimaan = DB::table('penerimaan')
            ->where('idpengadaan', $id)
            ->orderBy('idpenerimaan')
            ->get(['idpenerimaan', 'created_at as waktu', 'status']);

        return view('pengadaan.show', compact('pengadaan', 'detail', 'penerimaan'));
    }
}
