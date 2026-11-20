<?php

namespace App\Http\Controllers;

use App\Support\Sp;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  SP 9  sp_tambah_detail_retur  (detail retur)
 *  SP 8  sp_buat_retur           (header retur)
 *  TG 3  trg_after_insert_detail_retur (otomatis: stok berkurang, pengadaan dibuka lagi)
 */
class ReturController extends Controller
{
    public function index()
    {
        $retur = DB::table('retur as r')
            ->join('penerimaan as pn', 'pn.idpenerimaan', '=', 'r.idpenerimaan')
            ->join('pengadaan as p', 'p.idpengadaan', '=', 'pn.idpengadaan')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'r.iduser')
            ->leftJoin('detail_retur as dr', 'dr.idretur', '=', 'r.idretur')
            ->groupBy('r.idretur', 'r.created_at', 'r.idpenerimaan', 'v.nama_vendor', 'u.username')
            ->select(
                'r.idretur', 'r.created_at as waktu', 'r.idpenerimaan', 'v.nama_vendor', 'u.username',
                DB::raw('COALESCE(SUM(dr.jumlah), 0) as total_jumlah')
            )
            ->orderByDesc('r.idretur')
            ->paginate(10);

        return view('retur.index', compact('retur'));
    }

    /**
     * Tanpa ?penerimaan=ID  -> pilih penerimaan yang barangnya mau diretur.
     * Dengan ?penerimaan=ID -> isi jumlah dan alasan retur per barang.
     */
    public function create(Request $request)
    {
        $idPenerimaan = $request->query('penerimaan');

        if (! $idPenerimaan) {
            $daftar = DB::table('penerimaan as r')
                ->join('pengadaan as p', 'p.idpengadaan', '=', 'r.idpengadaan')
                ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
                ->select('r.idpenerimaan', 'r.created_at as waktu', 'r.idpengadaan', 'r.status', 'v.nama_vendor')
                ->orderByDesc('r.idpenerimaan')
                ->limit(30)
                ->get();

            return view('retur.pilih', compact('daftar'));
        }

        $penerimaan = DB::table('penerimaan as r')
            ->join('pengadaan as p', 'p.idpengadaan', '=', 'r.idpengadaan')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->where('r.idpenerimaan', $idPenerimaan)
            ->select('r.idpenerimaan', 'r.idpengadaan', 'r.created_at as waktu', 'v.nama_vendor')
            ->first();

        abort_if($penerimaan === null, 404);

        $detail = DB::table('detail_penerimaan as d')
            ->join('barang as b', 'b.idbarang', '=', 'd.barang_idbarang')
            ->where('d.idpenerimaan', $idPenerimaan)
            ->select(
                'd.iddetail_penerimaan', 'b.nama', 'd.jumlah_terima',
                DB::raw('(SELECT COALESCE(SUM(dr.jumlah), 0) FROM detail_retur dr WHERE dr.iddetail_penerimaan = d.iddetail_penerimaan) as sudah_retur')
            )
            ->get();

        return view('retur.create', compact('penerimaan', 'detail'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idpenerimaan' => ['required', 'integer', 'exists:penerimaan,idpenerimaan'],
            'retur'        => ['required', 'array'],
            'retur.*'      => ['nullable', 'integer', 'min:0'],
            'alasan'       => ['nullable', 'array'],
            'alasan.*'     => ['nullable', 'string', 'max:200'],
        ]);

        $baris = [];
        foreach ($data['retur'] as $idDetail => $jumlah) {
            if ((int) $jumlah <= 0) {
                continue;
            }

            $alasan = trim((string) ($data['alasan'][$idDetail] ?? ''));
            if ($alasan === '') {
                return back()->withInput()->withErrors([
                    'alasan' => 'Alasan retur wajib diisi untuk setiap barang yang diretur.',
                ]);
            }

            $baris[] = ['iddetail' => (int) $idDetail, 'jumlah' => (int) $jumlah, 'alasan' => $alasan];
        }

        if ($baris === []) {
            return back()->withInput()->withErrors(['retur' => 'Isi jumlah retur minimal untuk satu barang.']);
        }

        $idUser = $request->session()->get('iduser');

        try {
            $id = DB::transaction(function () use ($data, $baris, $idUser) {
                // SP 8: sp_buat_retur — header retur
                DB::statement('CALL sp_buat_retur(?, ?, @id_baru)', [(int) $data['idpenerimaan'], $idUser]);
                $id = (int) DB::selectOne('SELECT @id_baru AS id')->id;

                // SP 9: sp_tambah_detail_retur — validasi jumlah dan alasan.
                // TG 3 (otomatis di database): trg_after_insert_detail_retur mengurangi stok di kartu_stok
                // dan mengembalikan pengadaan ke status proses bila ada kekurangan.
                foreach ($baris as $b) {
                    DB::statement('CALL sp_tambah_detail_retur(?, ?, ?, ?)', [
                        $id, $b['iddetail'], $b['jumlah'], $b['alasan'],
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

        return redirect()->route('retur.show', $id)
            ->with('success', 'Retur dicatat. Stok berkurang; bila pengadaan sudah selesai, statusnya kembali proses.');
    }

    public function show(string $id)
    {
        $retur = DB::table('retur as r')
            ->join('penerimaan as pn', 'pn.idpenerimaan', '=', 'r.idpenerimaan')
            ->join('pengadaan as p', 'p.idpengadaan', '=', 'pn.idpengadaan')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'r.iduser')
            ->where('r.idretur', $id)
            ->select('r.idretur', 'r.created_at as waktu', 'r.idpenerimaan', 'pn.idpengadaan', 'p.status as status_pengadaan', 'v.nama_vendor', 'u.username')
            ->first();

        abort_if($retur === null, 404);

        $detail = DB::table('detail_retur as dr')
            ->join('detail_penerimaan as d', 'd.iddetail_penerimaan', '=', 'dr.iddetail_penerimaan')
            ->join('barang as b', 'b.idbarang', '=', 'd.barang_idbarang')
            ->where('dr.idretur', $id)
            ->select('b.nama', 'dr.jumlah', 'dr.alasan')
            ->get();

        return view('retur.show', compact('retur', 'detail'));
    }
}
