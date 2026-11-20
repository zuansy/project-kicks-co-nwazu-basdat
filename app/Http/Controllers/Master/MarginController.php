<?php

namespace App\Http\Controllers\Master;

use App\Support\Sp;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

/**
 * Objek database yang dipakai di controller ini (nomor sama dengan database/DAFTAR-SP-FN-TG.md):
 *  SP 12 sp_aktifkan_margin  (hanya satu margin aktif)
 */
class MarginController extends MasterController
{
    protected function konfig(): array
    {
        return [
            'judul' => 'Margin Penjualan',
            'rute'  => 'margin',
            'tabel' => 'margin_penjualan',
            'pk'    => 'idmargin_penjualan',
            'kolom' => [
                ['label' => 'ID', 'isi' => fn ($r) => $r->idmargin_penjualan],
                ['label' => 'Persentase', 'isi' => fn ($r) => rtrim(rtrim(number_format($r->persen, 2, ',', '.'), '0'), ',').'%'],
                ['label' => 'Status', 'isi' => fn ($r) => $r->status == 1
                    ? new HtmlString('<span class="badge bg-success">Aktif</span>')
                    : new HtmlString('<span class="badge bg-secondary">Non-aktif</span>')],
                ['label' => 'Dibuat oleh', 'isi' => fn ($r) => $r->username ?? '—'],
            ],
            // tombol tambahan di kolom Aksi: Aktifkan (hanya untuk margin non-aktif)
            'aksi' => fn ($r) => $r->status == 1 ? null : new HtmlString(
                '<form action="'.e(route('margin.aktifkan', $r->idmargin_penjualan)).'" method="POST" class="d-inline">'
                .csrf_field()
                .'<button type="submit" class="btn btn-sm btn-outline-success">Aktifkan</button></form>'
            ),
            'field' => [
                [
                    'nama' => 'persen', 'label' => 'Persentase margin (%)', 'tipe' => 'number',
                    'aturan' => fn () => ['required', 'numeric', 'min:0.01', 'max:100'],
                    'bantuan' => 'Harga jual = harga dasar + persentase ini.',
                ],
                [
                    'nama' => 'status', 'label' => 'Status', 'tipe' => 'select',
                    'opsi' => fn () => ['0' => 'Non-aktif', '1' => 'Aktif'],
                    'aturan' => fn () => ['required', 'in:0,1'],
                    'bantuan' => 'Hanya SATU margin yang boleh aktif. Mengaktifkan margin ini otomatis menonaktifkan yang lain.',
                ],
            ],
        ];
    }

    protected function daftar(): Builder
    {
        return DB::table('margin_penjualan as m')
            ->leftJoin('user as u', 'u.iduser', '=', 'm.iduser')
            ->select('m.idmargin_penjualan', 'm.persen', 'm.status', 'u.username')
            ->orderBy('m.idmargin_penjualan');
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(null));

        $id = DB::table('margin_penjualan')->insertGetId([
            'created_at' => now(),
            'persen'     => $data['persen'],
            'status'     => 0,
            'iduser'     => $request->session()->get('iduser'),
            'updated_at' => now(),
        ]);

        if ((int) $data['status'] === 1) {
            // SP 12: sp_aktifkan_margin — hanya SATU margin yang boleh aktif
            DB::statement('CALL sp_aktifkan_margin(?)', [$id]);
        }

        return redirect()->route('margin.index')->with('success', 'Margin penjualan berhasil ditambahkan.');
    }

    public function update(Request $request, string $id)
    {
        $baris = $this->cari($id);
        $data = $request->validate($this->aturan($baris));

        DB::table('margin_penjualan')->where('idmargin_penjualan', $baris->idmargin_penjualan)
            ->update(['persen' => $data['persen'], 'updated_at' => now()]);

        if ((int) $data['status'] === 1) {
            // SP 12: sp_aktifkan_margin — hanya SATU margin yang boleh aktif
            DB::statement('CALL sp_aktifkan_margin(?)', [$baris->idmargin_penjualan]);
        } else {
            DB::table('margin_penjualan')->where('idmargin_penjualan', $baris->idmargin_penjualan)
                ->update(['status' => 0]);
        }

        return redirect()->route('margin.index')->with('success', 'Margin penjualan berhasil diperbarui.');
    }

    public function aktifkan(string $id)
    {
        try {
            // SP 12: sp_aktifkan_margin — hanya SATU margin yang boleh aktif
            DB::statement('CALL sp_aktifkan_margin(?)', [(int) $id]);
        } catch (QueryException $e) {
            $pesan = Sp::pesan($e);
            if ($pesan === null) {
                throw $e;
            }

            return redirect()->route('margin.index')->with('error', $pesan);
        }

        return redirect()->route('margin.index')
            ->with('success', 'Margin diaktifkan. Margin lain otomatis non-aktif.');
    }
}
