@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <style>
        .stat { border: 0; border-radius: 1rem; color: #fff; position: relative; overflow: hidden; height: 100%; }
        .stat .card-body { padding: 1.25rem 1.4rem; }
        .stat .angka { font-size: 2rem; font-weight: 800; line-height: 1.1; }
        .stat .emoji { position: absolute; right: 1rem; bottom: .4rem; font-size: 3.2rem; opacity: .35; transform: rotate(-12deg); }
        .stat-1 { background: linear-gradient(135deg, #0d9be0, #12c2e9); }
        .stat-2 { background: linear-gradient(135deg, #0b2a4a, #14568f); }
        .stat-3 { background: linear-gradient(135deg, #ff7a1a, #ffb347); }
        .stat-4 { background: linear-gradient(135deg, #12a37f, #4ad6a7); }
    </style>

    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Halo, ' . session('username') . '!', 'crumb' => 'Ringkasan'])

                <div class="row g-3 mb-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat stat-1"><div class="card-body">
                            <div class="small text-uppercase opacity-75">Barang aktif</div>
                            <div class="angka">{{ number_format($totalBarang, 0, ',', '.') }}</div>
                            <span class="emoji" aria-hidden="true">👟</span>
                        </div></div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat stat-2"><div class="card-body">
                            <div class="small text-uppercase opacity-75">Total stok</div>
                            <div class="angka">{{ number_format($totalStok, 0, ',', '.') }}</div>
                            <span class="emoji" aria-hidden="true">📦</span>
                        </div></div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat stat-3"><div class="card-body">
                            <div class="small text-uppercase opacity-75">Pengadaan dalam proses</div>
                            <div class="angka">{{ number_format($pengadaanMenunggu, 0, ',', '.') }}</div>
                            <span class="emoji" aria-hidden="true">🚚</span>
                        </div></div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="card stat stat-4"><div class="card-body">
                            <div class="small text-uppercase opacity-75">Penjualan bulan ini</div>
                            <div class="angka">{{ number_format($penjualanBulanIni, 0, ',', '.') }}</div>
                            <span class="emoji" aria-hidden="true">🛒</span>
                        </div></div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="mb-3">⚠️ Stok menipis (≤ {{ $batas }})</h5>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr><th>Barang</th><th class="text-end">Stok</th><th class="text-end">Aksi</th></tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($menipis as $b)
                                                <tr>
                                                    <td>{{ $b->nama }}</td>
                                                    <td class="text-end">
                                                        <span class="badge {{ $b->stok == 0 ? 'bg-danger' : 'bg-warning text-dark' }}">{{ $b->stok }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="{{ route('kartustok.index', ['barang' => $b->idbarang]) }}"
                                                            class="btn btn-sm btn-outline-primary">Kartu stok</a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="3" class="text-center text-muted py-4">Semua stok aman 🎉</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card h-100">
                            <div class="card-body d-grid gap-2 align-content-start">
                                <h5 class="mb-2">Aksi cepat</h5>
                                <a href="{{ route('pengadaan.create') }}" class="btn btn-primary">🚚 Catat pengadaan</a>
                                <a href="{{ route('penerimaan.create') }}" class="btn btn-success">📥 Terima barang</a>
                                <a href="{{ route('penjualan.create') }}" class="btn btn-warning">🛒 Catat penjualan</a>
                                <a href="{{ route('retur.create') }}" class="btn btn-outline-danger">↩️ Catat retur</a>
                                <a href="{{ route('barang.create') }}" class="btn btn-outline-primary">👟 Tambah barang</a>
                                <a href="{{ route('toko') }}" class="btn btn-outline-secondary">🛍️ Lihat etalase toko</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
