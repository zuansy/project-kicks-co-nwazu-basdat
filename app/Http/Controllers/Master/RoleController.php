<?php

namespace App\Http\Controllers\Master;

use Illuminate\Validation\Rule;

class RoleController extends MasterController
{
    protected function konfig(): array
    {
        return [
            'judul' => 'Role',
            'rute'  => 'role',
            'tabel' => 'role',
            'pk'    => 'idrole',
            'kolom' => [
                ['label' => 'ID', 'isi' => fn ($r) => $r->idrole],
                ['label' => 'Nama Role', 'isi' => fn ($r) => $r->nama_role],
            ],
            'field' => [
                [
                    'nama' => 'nama_role', 'label' => 'Nama role', 'tipe' => 'text',
                    'aturan' => fn (?object $b) => [
                        'required', 'string', 'max:100',
                        Rule::unique('role', 'nama_role')->ignore($b?->idrole, 'idrole'),
                    ],
                ],
            ],
        ];
    }
}
