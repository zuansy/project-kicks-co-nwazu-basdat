@extends('layouts.auth')

@section('title', 'Daftar')
@section('subtitle', 'Buat akun untuk mulai belanja di Nwazu Kicks')

@section('form')
    <form action="{{ route('daftar.proses') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold small" for="username">Nama pengguna</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                <input type="text" id="username" class="form-control" name="username" value="{{ old('username') }}"
                    placeholder="contoh: sneakerhead_01" autocomplete="username" autofocus required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small" for="email">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}"
                    placeholder="nama@email.com" autocomplete="email" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold small" for="password">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password" class="form-control" name="password"
                    placeholder="Minimal 8 karakter" autocomplete="new-password" required>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold small" for="password_confirmation">Ulangi password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" id="password_confirmation" class="form-control" name="password_confirmation"
                    placeholder="Ketik ulang password" autocomplete="new-password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-kicks w-100 py-2 fw-semibold shadow-sm">
            <i class="fa-solid fa-shoe-prints me-1"></i> Buat Akun
        </button>
    </form>

    <div class="auth-links text-center mt-4">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        <div class="mt-2"><a href="{{ route('toko') }}">&larr; Lihat koleksi sepatu</a></div>
    </div>
@endsection
