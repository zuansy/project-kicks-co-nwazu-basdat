<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->has('iduser')) {
            return redirect($this->beranda($request));
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'login'    => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        $query = DB::table('user as u')
            ->leftJoin('role as r', 'r.idrole', '=', 'u.idrole')
            ->select('u.iduser', 'u.username', 'u.password', 'r.nama_role');

        if ($this->adaKolomEmail()) {
            // Boleh masuk dengan email ATAU username (akun karyawan lama tetap jalan).
            $query->addSelect('u.email')->where(function ($q) use ($data) {
                $q->where('u.email', $data['login'])->orWhere('u.username', $data['login']);
            });
        } else {
            $query->where('u.username', $data['login']);
        }

        $user = $query->first();

        if (! $user || ! $this->passwordCocok($data['password'], (string) $user->password)) {
            return back()
                ->withInput($request->only('login'))
                ->with('error', 'Email/username atau password salah.');
        }

        $this->mulaiSession($request, $user);

        if ($request->session()->get('staff')) {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('toko')->with('success', 'Selamat datang, '.$user->username.'!');
    }

    public function showDaftar(Request $request)
    {
        if ($request->session()->has('iduser')) {
            return redirect($this->beranda($request));
        }

        return view('daftar');
    }

    public function daftar(Request $request)
    {
        if (! $this->adaKolomEmail()) {
            return back()->withInput($request->only('username', 'email'))->with(
                'error',
                'Pendaftaran belum aktif: jalankan dulu script database/perubahan-login-email.sql di HeidiSQL.'
            );
        }

        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:45', 'alpha_dash', 'unique:user,username'],
            'email'    => ['required', 'email', 'max:100', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'max:100', 'confirmed'],
        ]);

        $idRole = $this->idRolePelanggan();

        $idUser = DB::table('user')->insertGetId([
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'idrole'   => $idRole,
        ]);

        $request->session()->regenerate();
        $request->session()->put([
            'iduser'    => $idUser,
            'username'  => $data['username'],
            'email'     => $data['email'],
            'nama_role' => config('inventori.role_pelanggan'),
            'staff'     => false,
        ]);

        return redirect()->route('toko')
            ->with('success', 'Akun berhasil dibuat. Selamat datang, '.$data['username'].'!');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('toko')->with('success', 'Kamu sudah keluar.');
    }

    private function mulaiSession(Request $request, object $user): void
    {
        // Karyawan = punya role selain Pelanggan.
        $staff = $user->nama_role !== null
            && $user->nama_role !== config('inventori.role_pelanggan');

        $request->session()->regenerate();
        $request->session()->put([
            'iduser'    => $user->iduser,
            'username'  => $user->username,
            'email'     => $user->email ?? null,
            'nama_role' => $user->nama_role,
            'staff'     => $staff,
        ]);
    }

    private function beranda(Request $request): string
    {
        return $request->session()->get('staff') ? route('dashboard') : route('toko');
    }

    private function adaKolomEmail(): bool
    {
        return Schema::hasColumn('user', 'email');
    }

    private function idRolePelanggan(): int
    {
        $nama = config('inventori.role_pelanggan');

        $id = DB::table('role')->where('nama_role', $nama)->value('idrole');

        return (int) ($id ?: DB::table('role')->insertGetId(['nama_role' => $nama]));
    }

    /**
     * Mendukung password ter-hash (bcrypt/argon) maupun teks biasa
     * (akun lama dari praktikum).
     */
    private function passwordCocok(string $input, string $tersimpan): bool
    {
        if (password_get_info($tersimpan)['algo'] !== null) {
            return password_verify($input, $tersimpan);
        }

        return hash_equals($tersimpan, $input);
    }
}
