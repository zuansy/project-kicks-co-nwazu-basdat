<?php

namespace App\Http\Controllers\Master;

class SatuanController extends MasterController
{
    protected function konfig(): array
    {
        return [
            'judul' => 'Satuan',
            'rute'  => 'satuan',
            'tabel' => 'satuan',
            'pk'    => 'idsatuan',
            'kolom' => [
                ['label' => 'ID', 'isi' => fn ($r) => $r->idsatuan],
                ['label' => 'Nama Satuan', 'isi' => fn ($r) => $r->nama_satuan],
                ['label' => 'Status', 'isi' => fn ($r) => $r->status == 1 ? 'Aktif' : 'Non-aktif'],
            ],
            'field' => [
                [
                    'nama' => 'nama_satuan', 'label' => 'Nama satuan', 'tipe' => 'text',
                    'aturan' => fn () => ['required', 'string', 'max:45'],
                ],
                [
                    'nama' => 'status', 'label' => 'Status', 'tipe' => 'select',
                    'opsi' => fn () => ['1' => 'Aktif', '0' => 'Non-aktif'],
                    'aturan' => fn () => ['required', 'in:0,1'],
                ],
            ],
        ];
    }
}
