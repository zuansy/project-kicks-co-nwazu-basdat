@once
    <style>
        .page-banner {
            position: relative; overflow: hidden; border-radius: 1rem; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; color: #fff;
            background:
                linear-gradient(105deg, rgba(8,40,80,.94) 0%, rgba(13,155,224,.82) 60%, rgba(18,194,233,.55) 100%),
                url('{{ asset('sepatuz.png') }}') center / cover no-repeat;
            box-shadow: 0 10px 30px rgba(13,155,224,.25);
        }
        .page-banner h1 { color: #fff; margin: 0; font-weight: 700; }
        .page-banner .crumb { font-size: .85rem; opacity: .9; }
        .page-banner .crumb a { color: #fff; text-decoration: none; }
        .page-banner .crumb a:hover { text-decoration: underline; }
        .page-banner .banner-emoji {
            position: absolute; right: 1.5rem; top: 50%; font-size: 3.5rem; opacity: .9;
            transform: translateY(-50%) rotate(-12deg); filter: drop-shadow(0 4px 8px rgba(0,0,0,.3));
        }
    </style>
@endonce
<section class="scroll-section" id="title">
    <div class="page-banner">
        <h1 class="display-6">{{ $judul }}</h1>
        <div class="crumb"><a href="{{ route('dashboard') }}">Dashboard</a> &rsaquo; {{ $crumb ?? $judul }}</div>
        <span class="banner-emoji" aria-hidden="true">👟</span>
    </div>
</section>
@include('layouts.alerts')
