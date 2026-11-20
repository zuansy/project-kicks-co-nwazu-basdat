<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Koleksi Sepatu') | Nwazu Kicks &amp; Co.</title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%91%9F%3C/text%3E%3C/svg%3E">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --navy: #0b2a4a; --blue: #0d9be0; --cyan: #12c2e9; --pop: #ff7a1a; }
        body { background: #f3f9fd; color: #12263f; }
        .navbar-kicks { background: var(--navy); }
        .navbar-kicks .navbar-brand { color: #fff; font-weight: 800; letter-spacing: .3px; }
        .navbar-kicks .navbar-brand .emoji { display: inline-block; transform: rotate(-10deg); }
        .btn-pop { background: var(--pop); border-color: var(--pop); color: #fff; font-weight: 600; }
        .btn-pop:hover { filter: brightness(1.08); color: #fff; }

        .hero {
            color: #fff; padding: 84px 0 76px;
            background:
                linear-gradient(105deg, rgba(8,40,80,.95) 0%, rgba(13,155,224,.80) 58%, rgba(18,194,233,.25) 100%),
                url('{{ asset('sepatuz.png') }}') center / cover no-repeat;
        }
        .hero h1 { font-weight: 800; font-size: clamp(2.1rem, 5vw, 3.4rem); line-height: 1.1; }
        .hero .tag { display: inline-block; background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.35);
                     padding: .25rem .8rem; border-radius: 999px; font-size: .85rem; margin-bottom: 1rem; }

        .chip { border: 1px solid #b9dcf0; color: var(--navy); background: #fff; padding: .35rem .95rem; border-radius: 999px;
                text-decoration: none; font-size: .9rem; font-weight: 600; }
        .chip:hover { border-color: var(--blue); color: var(--blue); }
        .chip.active { background: var(--blue); border-color: var(--blue); color: #fff; }

        .produk { border: 0; border-radius: 1rem; overflow: hidden; background: #fff;
                  box-shadow: 0 6px 22px rgba(13,155,224,.12); transition: transform .15s, box-shadow .15s; height: 100%; }
        .produk:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(13,155,224,.22); }
        .produk .foto { height: 150px; display: flex; align-items: center; justify-content: center; font-size: 4.2rem;
                        background: linear-gradient(135deg, #d9f3ff, #bfe8fb 55%, #9fdcf6); }
        .produk .foto span { display: inline-block; transform: rotate(-12deg); filter: drop-shadow(0 6px 8px rgba(11,42,74,.3)); }
        .produk .harga { color: var(--navy); font-weight: 800; font-size: 1.15rem; }
        .stok-ok { background: #e3f8ec; color: #167a43; }
        .stok-habis { background: #fdeaea; color: #b3261e; }

        footer.kicks { background: var(--navy); color: #b8d2e8; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand navbar-kicks sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('toko') }}"><span class="emoji">👟</span> Nwazu Kicks</a>
            <div class="ms-auto d-flex align-items-center gap-2">
                @if (session('iduser'))
                    <span class="text-white-50 small d-none d-sm-inline">Hai, <strong class="text-white">{{ session('username') }}</strong></span>
                    @if (session('staff'))
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-pop">Dashboard</a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-light">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-light">Masuk</a>
                    <a href="{{ route('daftar') }}" class="btn btn-sm btn-pop">Daftar</a>
                @endif
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        @if (session('success'))
            <div class="alert alert-success mb-0" role="alert">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-0" role="alert">{{ session('error') }}</div>
        @endif
    </div>

    @yield('content')

    <footer class="kicks mt-5 py-4">
        <div class="container d-flex flex-wrap justify-content-between gap-2 small">
            <span>&copy; {{ date('Y') }} Nwazu Kicks &amp; Co. &middot; Langkah baru, gaya baru.</span>
            <span><i class="fa-solid fa-shoe-prints me-1"></i> Sneakers &amp; aksesoris pilihan</span>
        </div>
    </footer>
</body>
</html>
