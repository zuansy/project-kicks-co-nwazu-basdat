<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  FN 1  fn_cek_sisa_stok      (kolom stok di daftar barang)
 *  SP 1  sp_tambah_barang      (tambah barang)
 *  SP 2  sp_nonaktifkan_barang (hapus = nonaktifkan / soft delete)
 */
class BarangController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        // FN 1: Menggunakan function fn_cek_sisa_stok untuk menampilkan stok
        $barang = DB::table('barang as b')
            ->leftJoin('satuan as s', 's.idsatuan', '=', 'b.idsatuan')
            ->select(
                'b.idbarang',
                'b.nama as nama_barang',
                'b.jenis',
                'b.harga',
                'b.status',
                's.nama_satuan',
                DB::raw('fn_cek_sisa_stok(b.idbarang) as stok')
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
        $data = $this->validasi($request, true);

        // SP 1: Memanggil Stored Procedure untuk Tambah Barang (status otomatis aktif, stok 0)
        DB::statement('CALL sp_tambah_barang(?, ?, ?, ?)', [
            $data['jenis'],
            $data['nama'],
            $data['idsatuan'],
            $data['harga'],
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan (via SP).');
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
        $barang->update($this->validasi($request, false));

        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        // SP 2: Memanggil Stored Procedure untuk Nonaktifkan Barang (Soft Delete)
        DB::statement('CALL sp_nonaktifkan_barang(?)', [$id]);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dinonaktifkan (via SP).');
    }

    private function daftarSatuan()
    {
        return Satuan::where('status', 1)->orderBy('nama_satuan')->get();
    }

    private function validasi(Request $request, bool $baru): array
    {
        return $request->validate([
            'nama'     => ['required', 'string', 'max:45'],
            'jenis'    => ['required', Rule::in(array_keys(Barang::JENIS))],
            'idsatuan' => ['required', 'integer', 'exists:satuan,idsatuan'],
            'harga'    => ['required', 'integer', 'min:0'],
            // Barang baru selalu aktif (diatur SP 1); status hanya diisi saat edit.
            'status'   => $baru ? ['nullable'] : ['required', Rule::in(['0', '1'])],
        ]);
    }
}
