@extends('layouts.auth')

@section('title', 'Masuk')
@section('subtitle', 'Masuk untuk lanjut belanja atau mengelola toko')

@section('form')
    <form action="{{ route('login.proses') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold small" for="login">Email atau username</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                <input type="text" id="login" class="form-control" name="login" value="{{ old('login') }}"
                    placeholder="nama@email.com" autocomplete="username" autofocus required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small" for="password">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password" class="form-control" name="password"
                    placeholder="Masukkan password" autocomplete="current-password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-kicks w-100 py-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-shoe-prints me-1"></i> Masuk
        </button>
    </form>

    <div class="auth-links text-center mt-4">
        Belum punya akun? <a href="{{ route('daftar') }}">Daftar gratis</a>
        <div class="mt-2"><a href="{{ route('toko') }}">&larr; Lihat koleksi sepatu</a></div>
    </div>
@endsection
