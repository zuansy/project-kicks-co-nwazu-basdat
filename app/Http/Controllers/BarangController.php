<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // Stok = saldo terakhir per barang dari kartu_stok.
        $saldoTerakhir = DB::table('kartu_stok as k')
            ->joinSub(
                DB::table('kartu_stok')
                    ->select('idbarang', DB::raw('MAX(idkartu_stok) as last_id'))
                    ->groupBy('idbarang'),
                'm',
                'm.last_id',
                '=',
                'k.idkartu_stok'
            )
            ->select('k.idbarang', 'k.stock');

        $barang = DB::table('barang as b')
            ->leftJoin('satuan as s', 's.idsatuan', '=', 'b.idsatuan')
            ->leftJoinSub($saldoTerakhir, 'st', 'st.idbarang', '=', 'b.idbarang')
            ->select(
                'b.idbarang',
                'b.nama as nama_barang',
                'b.jenis',
                'b.harga',
                'b.status',
                's.nama_satuan',
                DB::raw('COALESCE(st.stock, 0) as stok')
            )
            ->when($q !== '', fn ($query) => $query->where('b.nama', 'like', "%{$q}%"))
            ->orderBy('b.nama')
            ->paginate(10)
            ->withQueryString();

        return view('barang.index', compact('barang', 'q'));
    }

    public function create()
    {
        return view('barang.create', [
            'satuan' => $this->daftarSatuan(),
            'jenis'  => Barang::JENIS,
        ]);
    }

    public function store(Request $request)
    {
        Barang::create($this->validasi($request));

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        return view('barang.edit', [
            'barang' => Barang::findOrFail($id),
            'satuan' => $this->daftarSatuan(),
            'jenis'  => Barang::JENIS,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $barang = Barang::findOrFail($id);
        $barang->update($this->validasi($request));

        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $barang = Barang::findOrFail($id);

        try {
            $barang->delete();
        } catch (QueryException $e) {
            // 23000 = pelanggaran foreign key (barang sudah dipakai transaksi)
            if ((string) $e->getCode() !== '23000') {
                throw $e;
            }

            return redirect()->route('barang.index')->with(
                'error',
                'Barang "'.$barang->nama.'" sudah dipakai di transaksi sehingga tidak bisa dihapus. '
                .'Ubah statusnya menjadi Nonaktif saja.'
            );
        }

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    private function daftarSatuan()
    {
        return Satuan::where('status', 1)->orderBy('nama_satuan')->get();
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama'     => ['required', 'string', 'max:45'],
            'jenis'    => ['required', Rule::in(array_keys(Barang::JENIS))],
            'idsatuan' => ['required', 'integer', 'exists:satuan,idsatuan'],
            'harga'    => ['required', 'integer', 'min:0'],
            'status'   => ['required', Rule::in(['0', '1'])],
        ]);
    }
}
