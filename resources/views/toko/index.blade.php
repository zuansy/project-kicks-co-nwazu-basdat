@extends('layouts.toko')

@section('title', 'Koleksi Sepatu')

@section('content')
    @php
        $ikon = ['S' => '👟', 'A' => '🧦'];
    @endphp

    <header class="hero">
        <div class="container">
            <span class="tag"><i class="fa-solid fa-bolt me-1"></i> Koleksi terbaru</span>
            <h1 class="mb-3">Langkah baru,<br>gaya baru.</h1>
            <p class="lead mb-4" style="max-width: 520px; opacity: .92">
                Sneakers pilihan dari brand favoritmu, lengkap dengan aksesoris untuk menjaga tampilannya tetap segar.
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="#koleksi" class="btn btn-pop btn-lg px-4">Lihat koleksi</a>
                @unless (session('iduser'))
                    <a href="{{ route('daftar') }}" class="btn btn-outline-light btn-lg px-4">Buat akun</a>
                @endunless
            </div>
        </div>
    </header>

    <main class="container" id="koleksi" style="padding-top: 2.5rem">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('toko', array_filter(['q' => $q])) }}" class="chip {{ $jenis === '' ? 'active' : '' }}">Semua</a>
                @foreach ($daftarJenis as $kode => $label)
                    <a href="{{ route('toko', array_filter(['q' => $q, 'jenis' => $kode])) }}"
                        class="chip {{ $jenis === $kode ? 'active' : '' }}">{{ ($ikon[$kode] ?? '🛍️') . ' ' . $label }}</a>
                @endforeach
            </div>
            <form method="GET" action="{{ route('toko') }}" class="d-flex gap-2">
                @if ($jenis !== '')
                    <input type="hidden" name="jenis" value="{{ $jenis }}">
                @endif
                <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Cari sepatu...">
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <div class="row g-4">
            @forelse ($produk as $p)
                <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                    <div class="produk">
                        <div class="foto"><span>{{ $ikon[$p->jenis] ?? '🛍️' }}</span></div>
                        <div class="p-3">
                            <div class="small text-muted mb-1">{{ \App\Models\Barang::JENIS[$p->jenis] ?? 'Lainnya' }}</div>
                            <h6 class="fw-bold mb-2" style="min-height: 2.5em">{{ $p->nama }}</h6>
                            <div class="harga mb-2">Rp {{ number_format($p->harga, 0, ',', '.') }}
                                @if ($p->nama_satuan)
                                    <span class="small text-muted fw-normal">/ {{ strtolower($p->nama_satuan) }}</span>
                                @endif
                            </div>
                            @if ($p->stok > 0)
                                <span class="badge rounded-pill stok-ok px-3 py-2">Tersedia &middot; {{ $p->stok }} {{ strtolower($p->nama_satuan ?? '') }}</span>
                            @else
                                <span class="badge rounded-pill stok-habis px-3 py-2">Stok habis</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <div style="font-size: 3rem">🔍</div>
                        Belum ada produk yang cocok{{ $q !== '' ? ' dengan "' . $q . '"' : '' }}.
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-4">{{ $produk->links() }}</div>
    </main>
@endsection
