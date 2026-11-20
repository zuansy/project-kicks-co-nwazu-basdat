<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Kerangka CRUD sederhana untuk data master.
 * Subclass cukup mengisi konfig(): judul, rute, tabel, pk, kolom (tabel daftar), field (form).
 */
abstract class MasterController extends Controller
{
    /**
     * @return array{judul:string, rute:string, tabel:string, pk:string, kolom:array, field:array}
     */
    abstract protected function konfig(): array;

    protected function daftar(): Builder
    {
        $k = $this->konfig();

        return DB::table($k['tabel'])->orderBy($k['pk']);
    }

    public function index()
    {
        return view('master.index', [
            'k'    => $this->konfig(),
            'data' => $this->daftar()->paginate(15),
        ]);
    }

    public function create()
    {
        return view('master.form', ['k' => $this->konfig(), 'baris' => null]);
    }

    public function store(Request $request)
    {
        $k = $this->konfig();
        $data = $request->validate($this->aturan(null));

        $this->simpan(null, $this->siapkan($data, null));

        return redirect()->route($k['rute'].'.index')
            ->with('success', $k['judul'].' berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        return view('master.form', ['k' => $this->konfig(), 'baris' => $this->cari($id)]);
    }

    public function update(Request $request, string $id)
    {
        $k = $this->konfig();
        $baris = $this->cari($id);
        $data = $request->validate($this->aturan($baris));

        $this->simpan($baris, $this->siapkan($data, $baris));

        return redirect()->route($k['rute'].'.index')
            ->with('success', $k['judul'].' berhasil diperbarui.');
    }

    public function destroy(string $id)
    {
        $k = $this->konfig();
        $baris = $this->cari($id);

        try {
            DB::table($k['tabel'])->where($k['pk'], $baris->{$k['pk']})->delete();
        } catch (QueryException $e) {
            // 23000 = pelanggaran foreign key (data sudah dipakai tabel lain)
            if ((string) $e->getCode() !== '23000') {
                throw $e;
            }

            return redirect()->route($k['rute'].'.index')
                ->with('error', 'Data tidak bisa dihapus karena sudah dipakai di data lain.');
        }

        return redirect()->route($k['rute'].'.index')
            ->with('success', $k['judul'].' berhasil dihapus.');
    }

    protected function cari(string $id): object
    {
        $k = $this->konfig();
        $baris = DB::table($k['tabel'])->where($k['pk'], $id)->first();

        abort_if($baris === null, 404);

        return $baris;
    }

    /** Aturan validasi dari konfigurasi field. $baris null = tambah baru. */
    protected function aturan(?object $baris): array
    {
        $aturan = [];

        foreach ($this->konfig()['field'] as $f) {
            $aturan[$f['nama']] = ($f['aturan'])($baris);
        }

        return $aturan;
    }

    /** Ubah data tervalidasi sebelum disimpan (mis. hash password). */
    protected function siapkan(array $data, ?object $baris): array
    {
        return $data;
    }

    protected function simpan(?object $baris, array $data): void
    {
        $k = $this->konfig();

        if ($baris === null) {
            DB::table($k['tabel'])->insert($data);
        } else {
            DB::table($k['tabel'])->where($k['pk'], $baris->{$k['pk']})->update($data);
        }
    }
}
