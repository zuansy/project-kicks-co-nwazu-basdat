<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Nwazu Kicks & Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('{{ asset('sepatuz.png') }}');
            background-size: cover; background-position: center; background-attachment: fixed;
            background-color: #e0f7fa;
        }
        .overlay { background-color: rgba(0, 150, 255, 0.15); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-box {
            width: 100%; max-width: 420px;
            background-color: rgba(224, 247, 255, 0.85); backdrop-filter: blur(10px);
            padding: 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 100, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="login-box">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-dark"><i class="fa-solid fa-shoe-prints text-primary"></i> Nwazu Kicks</h2>
                <p class="text-muted small">Silakan login untuk masuk ke sistem inventori</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger py-2 small text-center">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success py-2 small text-center">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach($errors->all() as $pesan)<div>{{ $pesan }}</div>@endforeach
                </div>
            @endif

            <form action="{{ route('login.proses') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Username</label>
                    <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Masukkan username" autofocus required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold small">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">Masuk Sistem</button>
            </form>
        </div>
    </div>
</body>
</html>
