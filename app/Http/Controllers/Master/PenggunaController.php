<?php

namespace App\Http\Controllers\Master;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PenggunaController extends MasterController
{
    protected function konfig(): array
    {
        $adaEmail = Schema::hasColumn('user', 'email');

        $field = [
            [
                'nama' => 'username', 'label' => 'Username', 'tipe' => 'text',
                'aturan' => fn (?object $b) => [
                    'required', 'string', 'min:3', 'max:45', 'alpha_dash',
                    Rule::unique('user', 'username')->ignore($b?->iduser, 'iduser'),
                ],
            ],
        ];

        if ($adaEmail) {
            $field[] = [
                'nama' => 'email', 'label' => 'Email', 'tipe' => 'email',
                'aturan' => fn (?object $b) => [
                    'required', 'email', 'max:100',
                    Rule::unique('user', 'email')->ignore($b?->iduser, 'iduser'),
                ],
            ];
        }

        $field[] = [
            'nama' => 'password', 'label' => 'Password', 'tipe' => 'password',
            'aturan' => fn (?object $b) => $b === null
                ? ['required', 'string', 'min:8', 'max:100']
                : ['nullable', 'string', 'min:8', 'max:100'],
            'bantuan' => 'Saat edit, kosongkan jika password tidak diganti.',
        ];

        $field[] = [
            'nama' => 'idrole', 'label' => 'Role', 'tipe' => 'select',
            'opsi' => fn () => DB::table('role')->orderBy('nama_role')->pluck('nama_role', 'idrole')->all(),
            'aturan' => fn () => ['required', 'integer', 'exists:role,idrole'],
        ];

        $kolom = [
            ['label' => 'ID', 'isi' => fn ($r) => $r->iduser],
            ['label' => 'Username', 'isi' => fn ($r) => $r->username],
        ];
        if ($adaEmail) {
            $kolom[] = ['label' => 'Email', 'isi' => fn ($r) => $r->email ?? '—'];
        }
        $kolom[] = ['label' => 'Role', 'isi' => fn ($r) => $r->nama_role ?? '—'];

        return [
            'judul' => 'Pengguna',
            'rute'  => 'pengguna',
            'tabel' => 'user',
            'pk'    => 'iduser',
            'kolom' => $kolom,
            'field' => $field,
        ];
    }

    protected function daftar(): Builder
    {
        $email = Schema::hasColumn('user', 'email') ? 'u.email' : DB::raw('NULL as email');

        return DB::table('user as u')
            ->leftJoin('role as r', 'r.idrole', '=', 'u.idrole')
            ->select('u.iduser', 'u.username', $email, 'r.nama_role')
            ->orderBy('u.iduser');
    }

    protected function siapkan(array $data, ?object $baris): array
    {
        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    public function destroy(string $id)
    {
        if ((int) $id === (int) session('iduser')) {
            return redirect()->route('pengguna.index')
                ->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        return parent::destroy($id);
    }
}
