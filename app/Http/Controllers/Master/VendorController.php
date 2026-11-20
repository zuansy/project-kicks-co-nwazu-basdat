<?php

namespace App\Http\Controllers\Master;

class VendorController extends MasterController
{
    private const BADAN_HUKUM = ['P' => 'PT', 'C' => 'CV', 'I' => 'Perorangan / Toko'];

    protected function konfig(): array
    {
        return [
            'judul' => 'Vendor',
            'rute'  => 'vendor',
            'tabel' => 'vendor',
            'pk'    => 'idvendor',
            'kolom' => [
                ['label' => 'ID', 'isi' => fn ($r) => $r->idvendor],
                ['label' => 'Nama Vendor', 'isi' => fn ($r) => $r->nama_vendor],
                ['label' => 'Badan Hukum', 'isi' => fn ($r) => self::BADAN_HUKUM[$r->badan_hukum] ?? $r->badan_hukum],
                ['label' => 'Status', 'isi' => fn ($r) => $r->status === '1' ? 'Aktif' : 'Non-aktif'],
            ],
            'field' => [
                [
                    'nama' => 'nama_vendor', 'label' => 'Nama vendor', 'tipe' => 'text',
                    'aturan' => fn () => ['required', 'string', 'max:100'],
                ],
                [
                    'nama' => 'badan_hukum', 'label' => 'Badan hukum', 'tipe' => 'select',
                    'opsi' => fn () => self::BADAN_HUKUM,
                    'aturan' => fn () => ['required', 'in:'.implode(',', array_keys(self::BADAN_HUKUM))],
                ],
                [
                    'nama' => 'status', 'label' => 'Status', 'tipe' => 'select',
                    'opsi' => fn () => ['1' => 'Aktif', '0' => 'Non-aktif'],
                    'aturan' => fn () => ['required', 'in:0,1'],
                    'bantuan' => 'Hanya vendor aktif yang bisa dipilih di Pengadaan.',
                ],
            ],
        ];
    }
}
