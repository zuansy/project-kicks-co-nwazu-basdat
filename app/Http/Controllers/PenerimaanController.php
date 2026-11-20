<?php

namespace App\Http\Controllers;

use App\Support\Sp;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  SP 5  sp_buat_penerimaan                (header penerimaan)
 *  SP 6  sp_tambah_detail_penerimaan        (detail penerimaan)
 *  SP 7  sp_tutup_penerimaan                (status tuntas/sebagian, pengadaan selesai)
 *  FN 3  fn_sisa_pesanan                    (sisa pesanan)
 *  TG 1  trg_after_insert_detail_penerimaan (otomatis: stok masuk ke kartu_stok)
 */
class PenerimaanController extends Controller
{
    public function index()
    {
        $penerimaan = DB::table('penerimaan as r')
            ->join('pengadaan as p', 'p.idpengadaan', '=', 'r.idpengadaan')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'r.iduser')
            ->select('r.idpenerimaan', 'r.created_at as waktu', 'r.status', 'r.idpengadaan', 'v.nama_vendor', 'u.username')
            ->orderByDesc('r.idpenerimaan')
            ->paginate(10);

        return view('penerimaan.index', compact('penerimaan'));
    }

    /**
     * Tanpa ?pengadaan=ID  -> pilih pengadaan yang masih berstatus proses.
     * Dengan ?pengadaan=ID -> isi manual jumlah dan harga terima tiap barang.
     */
    public function create(Request $request)
    {
        $idPengadaan = $request->query('pengadaan');

        if (! $idPengadaan) {
            $terbuka = DB::table('pengadaan as p')
                ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
                ->where('p.status', config('inventori.status_pengadaan_proses'))
                ->select('p.idpengadaan', 'p.timestamp as waktu', 'v.nama_vendor', 'p.total_nilai')
                ->orderByDesc('p.idpengadaan')
                ->get();

            return view('penerimaan.pilih', compact('terbuka'));
        }

        $pengadaan = DB::table('pengadaan as p')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->where('p.idpengadaan', $idPengadaan)
            ->select('p.idpengadaan', 'p.timestamp as waktu', 'p.status', 'v.nama_vendor')
            ->first();

        abort_if($pengadaan === null, 404);

        if ($pengadaan->status !== config('inventori.status_pengadaan_proses')) {
            return redirect()->route('pengadaan.show', $idPengadaan)
                ->with('error', 'Pengadaan ini sudah selesai, semua barang sudah diterima.');
        }

        return view('penerimaan.create', [
            'pengadaan' => $pengadaan,
            'detail'    => DB::table('detail_pengadaan as dp')
                ->join('barang as b', 'b.idbarang', '=', 'dp.idbarang')
                ->where('dp.idpengadaan', $idPengadaan)
                ->select(
                    'dp.idbarang', 'b.nama', 'dp.jumlah', 'dp.harga_satuan',
                    DB::raw('fn_sisa_pesanan(dp.idpengadaan, dp.idbarang) as sisa') // FN 3: fn_sisa_pesanan
                )
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idpengadaan' => ['required', 'integer', 'exists:pengadaan,idpengadaan'],
            'terima'      => ['required', 'array'],
            'terima.*'    => ['nullable', 'integer', 'min:0'],
            'harga'       => ['required', 'array'],
            'harga.*'     => ['nullable', 'integer', 'min:0'],
        ]);

        $masuk = [];
        foreach ($data['terima'] as $idBarang => $jumlah) {
            if ((int) $jumlah <= 0) {
                continue;
            }

            $harga = $data['harga'][$idBarang] ?? null;
            if ($harga === null) {
                return back()->withInput()->withErrors([
                    'harga' => 'Harga satuan terima wajib diisi untuk barang yang diterima.',
                ]);
            }

            $masuk[] = ['idbarang' => (int) $idBarang, 'jumlah' => (int) $jumlah, 'harga' => (int) $harga];
        }

        if ($masuk === []) {
            return back()->withInput()->withErrors([
                'terima' => 'Isi jumlah diterima minimal untuk satu barang.',
            ]);
        }

        $idUser = $request->session()->get('iduser');

        try {
            $id = DB::transaction(function () use ($data, $masuk, $idUser) {
                // SP 5: sp_buat_penerimaan — header penerimaan baru (hanya untuk pengadaan berstatus proses)
                DB::statement('CALL sp_buat_penerimaan(?, ?, @id_baru)', [(int) $data['idpengadaan'], $idUser]);
                $id = (int) DB::selectOne('SELECT @id_baru AS id')->id;

                // SP 6: sp_tambah_detail_penerimaan — jumlah dan harga terima manual; cek sisa memakai FN 3.
                // TG 1 (otomatis di database): trg_after_insert_detail_penerimaan mengisi kartu_stok
                // setiap kali detail penerimaan di-insert oleh SP 6.
                foreach ($masuk as $m) {
                    DB::statement('CALL sp_tambah_detail_penerimaan(?, ?, ?, ?)', [
                        $id, $m['idbarang'], $m['jumlah'], $m['harga'],
                    ]);
                }

                // SP 7: sp_tutup_penerimaan — tentukan status tuntas/sebagian; bila tuntas pengadaan menjadi selesai
                DB::statement('CALL sp_tutup_penerimaan(?)', [$id]);

                return $id;
            });
        } catch (QueryException $e) {
            $pesan = Sp::pesan($e);
            if ($pesan === null) {
                throw $e;
            }

            return back()->withInput()->with('error', $pesan);
        }

        $tuntas = DB::table('penerimaan')->where('idpenerimaan', $id)->value('status')
            === config('inventori.status_penerimaan_tuntas');

        return redirect()->route('penerimaan.show', $id)->with(
            'success',
            $tuntas
                ? 'Penerimaan tuntas. Pengadaan otomatis selesai dan stok sudah bertambah.'
                : 'Penerimaan sebagian dicatat. Pengadaan masih proses sampai sisa barang diterima.'
        );
    }

    public function show(string $id)
    {
        $penerimaan = DB::table('penerimaan as r')
            ->join('pengadaan as p', 'p.idpengadaan', '=', 'r.idpengadaan')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'r.iduser')
            ->where('r.idpenerimaan', $id)
            ->select('r.idpenerimaan', 'r.created_at as waktu', 'r.status', 'r.idpengadaan', 'p.status as status_pengadaan', 'v.nama_vendor', 'u.username')
            ->first();

        abort_if($penerimaan === null, 404);

        $detail = DB::table('detail_penerimaan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.barang_idbarang')
            ->where('d.idpenerimaan', $id)
            ->select(
                'b.nama', 'd.jumlah_terima', 'd.harga_satuan_terima', 'd.sub_total_terima',
                DB::raw('(SELECT COALESCE(SUM(dr.jumlah), 0) FROM detail_retur dr WHERE dr.iddetail_penerimaan = d.iddetail_penerimaan) as diretur')
            )
            ->get();

        return view('penerimaan.show', compact('penerimaan', 'detail'));
    }
}
