<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if ($request->session()->has('iduser')) {
            return redirect()->route('barang.index');
        }

        return view('login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:45'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        $user = DB::table('user')->where('username', $data['username'])->first();

        if (! $user || ! $this->passwordCocok($data['password'], (string) $user->password)) {
            return back()
                ->withInput($request->only('username'))
                ->with('error', 'Username atau password salah.');
        }

        // Ganti ID session setelah login (mencegah session fixation).
        $request->session()->regenerate();
        $request->session()->put([
            'iduser'   => $user->iduser,
            'username' => $user->username,
        ]);

        return redirect()->intended(route('barang.index'));
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Kamu sudah keluar.');
    }

    /**
     * Mendukung password ter-hash (bcrypt/argon) maupun password teks biasa
     * yang mungkin masih tersimpan di tabel user dari praktikum.
     */
    private function passwordCocok(string $input, string $tersimpan): bool
    {
        if (password_get_info($tersimpan)['algo'] !== null) {
            return password_verify($input, $tersimpan);
        }

        return hash_equals($tersimpan, $input);
    }
}
