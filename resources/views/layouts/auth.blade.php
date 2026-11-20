<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Nwazu Kicks &amp; Co.</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%91%9F%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-image: url('{{ asset('sepatuz.png') }}');
            background-size: cover; background-position: center; background-attachment: fixed;
            background-color: #e0f7fa;
        }
        .overlay { background-color: rgba(0, 150, 255, 0.15); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px 12px; }
        .auth-box {
            width: 100%; max-width: 440px;
            background-color: rgba(224, 247, 255, 0.88); backdrop-filter: blur(10px);
            padding: 36px 40px; border-radius: 20px; box-shadow: 0 15px 35px rgba(0, 100, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.7);
        }
        .brand { font-weight: 800; color: #0b2a4a; text-decoration: none; }
        .brand .emoji { display: inline-block; transform: rotate(-10deg); font-size: 1.9rem; }
        .btn-kicks { background: linear-gradient(90deg, #0d6efd, #12c2e9); border: 0; color: #fff; }
        .btn-kicks:hover { filter: brightness(1.08); color: #fff; }
        .auth-links { font-size: .9rem; }
        .auth-links a { color: #0d6efd; text-decoration: none; font-weight: 600; }
        .auth-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="overlay">
        <div class="auth-box">
            <div class="text-center mb-4">
                <a href="{{ route('toko') }}" class="brand h2 d-inline-block mb-1">
                    <span class="emoji">👟</span> Nwazu Kicks
                </a>
                <p class="text-muted small mb-0">@yield('subtitle')</p>
            </div>

            @if (session('error'))
                <div class="alert alert-danger py-2 small text-center">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="alert alert-success py-2 small text-center">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    @foreach ($errors->all() as $pesan)<div>{{ $pesan }}</div>@endforeach
                </div>
            @endif

            @yield('form')
        </div>
    </div>
</body>
</html>
