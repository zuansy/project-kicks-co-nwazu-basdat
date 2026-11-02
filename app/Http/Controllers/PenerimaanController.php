<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    public function index()
    {
        $penerimaan = DB::table('penerimaan as r')
            ->join('pengadaan as p', 'p.idpengadaan', '=', 'r.idpengadaan')
            ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
            ->join('user as u', 'u.iduser', '=', 'r.iduser')
            ->select(
                'r.idpenerimaan',
                'r.created_at as waktu',
                'r.idpengadaan',
                'v.nama_vendor',
                'u.username'
            )
            ->orderByDesc('r.idpenerimaan')
            ->paginate(10);

        return view('penerimaan.index', compact('penerimaan'));
    }

    /**
     * Tanpa ?pengadaan=ID  -> pilih pengadaan yang masih terbuka.
     * Dengan ?pengadaan=ID -> form jumlah barang yang diterima.
     */
    public function create(Request $request)
    {
        $idPengadaan = $request->query('pengadaan');

        if (! $idPengadaan) {
            $terbuka = DB::table('pengadaan as p')
                ->join('vendor as v', 'v.idvendor', '=', 'p.vendor_idvendor')
                ->where('p.status', config('inventori.status_pengadaan_baru'))
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

        return view('penerimaan.create', [
            'pengadaan' => $pengadaan,
            'detail'    => $this->detailPengadaan((int) $idPengadaan),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'idpengadaan' => ['required', 'integer', 'exists:pengadaan,idpengadaan'],
            'terima'      => ['required', 'array'],
            'terima.*'    => ['nullable', 'integer', 'min:0'],
        ]);

        $idPengadaan = (int) $data['idpengadaan'];
        $detail = $this->detailPengadaan($idPengadaan)->keyBy('idbarang');

        $masuk = [];
        foreach ($data['terima'] as $idBarang => $jumlah) {
            $jumlah = (int) $jumlah;
            if ($jumlah <= 0) {
                continue;
            }

            $baris = $detail->get($idBarang);
            if ($baris === null) {
                return back()->withInput()->withErrors([
                    'terima' => 'Ada barang yang bukan bagian dari pengadaan ini.',
                ]);
            }

            $sisa = (int) $baris->jumlah - (int) $baris->diterima;
            if ($jumlah > $sisa) {
                return back()->withInput()->withErrors([
                    'terima' => 'Jumlah diterima untuk "'.$baris->nama.'" melebihi sisa pesanan ('.$sisa.').',
                ]);
            }

            $masuk[] = [
                'idbarang' => (int) $idBarang,
                'jumlah'   => $jumlah,
                'harga'    => (int) $baris->harga_satuan,
            ];
        }

        if ($masuk === []) {
            return back()->withInput()->withErrors([
                'terima' => 'Isi jumlah diterima minimal untuk satu barang.',
            ]);
        }

        $idUser = $request->session()->get('iduser');
        $catatStokManual = ! $this->stokDikelolaTrigger();

        $idPenerimaan = DB::transaction(function () use ($idPengadaan, $masuk, $idUser, $catatStokManual) {
            $idPenerimaan = DB::table('penerimaan')->insertGetId([
                'created_at'  => now(),
                'status'      => config('inventori.status_penerimaan'),
                'idpengadaan' => $idPengadaan,
                'iduser'      => $idUser,
            ]);

            foreach ($masuk as $m) {
                DB::table('detail_penerimaan')->insert([
                    'idpenerimaan'        => $idPenerimaan,
                    'barang_idbarang'     => $m['idbarang'],
                    'jumlah_terima'       => $m['jumlah'],
                    'harga_satuan_terima' => $m['harga'],
                    'sub_total_terima'    => $m['jumlah'] * $m['harga'],
                ]);

                if ($catatStokManual) {
                    $stokTerakhir = (int) DB::table('kartu_stok')
                        ->where('idbarang', $m['idbarang'])
                        ->orderByDesc('idkartu_stok')
                        ->lockForUpdate()
                        ->value('stock');

                    DB::table('kartu_stok')->insert([
                        'jenis_transaksi' => config('inventori.kartu_stok_penerimaan'),
                        'masuk'           => $m['jumlah'],
                        'keluar'          => 0,
                        'stock'           => $stokTerakhir + $m['jumlah'],
                        'created_at'      => now(),
                        'idtransaksi'     => $idPenerimaan,
                        'idbarang'        => $m['idbarang'],
                    ]);
                }
            }

            // Tutup pengadaan jika semua barang sudah diterima lengkap.
            $masihAdaSisa = $this->detailPengadaan($idPengadaan)
                ->contains(fn ($d) => (int) $d->jumlah - (int) $d->diterima > 0);

            if (! $masihAdaSisa) {
                DB::table('pengadaan')
                    ->where('idpengadaan', $idPengadaan)
                    ->update(['status' => config('inventori.status_pengadaan_selesai')]);
            }

            return $idPenerimaan;
        });

        return redirect()->route('penerimaan.index')
            ->with('success', 'Penerimaan #'.$idPenerimaan.' berhasil dicatat.');
    }

    /** Baris pengadaan beserta jumlah yang sudah diterima sebelumnya. */
    private function detailPengadaan(int $idPengadaan)
    {
        $sudahDiterima = DB::table('detail_penerimaan as dt')
            ->join('penerimaan as pn', 'pn.idpenerimaan', '=', 'dt.idpenerimaan')
            ->where('pn.idpengadaan', $idPengadaan)
            ->groupBy('dt.barang_idbarang')
            ->select('dt.barang_idbarang', DB::raw('SUM(dt.jumlah_terima) as diterima'));

        return DB::table('detail_pengadaan as dp')
            ->join('barang as b', 'b.idbarang', '=', 'dp.idbarang')
            ->leftJoinSub($sudahDiterima, 'rc', 'rc.barang_idbarang', '=', 'dp.idbarang')
            ->where('dp.idpengadaan', $idPengadaan)
            ->select(
                'dp.idbarang',
                'b.nama',
                'dp.jumlah',
                'dp.harga_satuan',
                DB::raw('COALESCE(rc.diterima, 0) as diterima')
            )
            ->get();
    }

    /**
     * Jika di database sudah ada trigger AFTER/BEFORE INSERT pada
     * detail_penerimaan, anggap trigger itulah yang mengisi kartu_stok
     * -- supaya stok tidak tercatat dua kali.
     */
    private function stokDikelolaTrigger(): bool
    {
        return DB::table('information_schema.TRIGGERS')
            ->where('TRIGGER_SCHEMA', DB::getDatabaseName())
            ->where('EVENT_OBJECT_TABLE', 'detail_penerimaan')
            ->where('EVENT_MANIPULATION', 'INSERT')
            ->exists();
    }
}
