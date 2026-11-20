@extends('layouts.app')

@section('title', 'Master Data')

@section('content')
    @php
        $admin = in_array(session('nama_role'), config('inventori.role_admin'), true);
        $bolehMargin = in_array(session('nama_role'), config('inventori.role_margin'), true);
        $ketMargin = $marginAktif !== null
            ? 'Margin aktif sekarang: ' . rtrim(rtrim(number_format($marginAktif, 2, ',', '.'), '0'), ',') . '%'
            : 'Belum ada margin aktif.';
        $kartu = [
            ['👟', 'Barang',           'barang.index',   $jumlah['barang'],   'Katalog barang, jenis, harga dasar.', true],
            ['📏', 'Satuan',           'satuan.index',   $jumlah['satuan'],   'Pasang, pcs, box, dan lainnya.', true],
            ['🏭', 'Vendor',           'vendor.index',   $jumlah['vendor'],   'Pemasok barang untuk pengadaan.', true],
            ['📈', 'Margin Penjualan', 'margin.index',   $jumlah['margin'],   $ketMargin, $bolehMargin],
            ['🛡️', 'Role',             'role.index',     $jumlah['role'],     'Jenis jabatan pengguna.', $admin],
            ['👥', 'Pengguna',         'pengguna.index', $jumlah['pengguna'], 'Akun karyawan dan pelanggan.', $admin],
        ];
    @endphp
    <style>
        .kartu-master { border: 0; border-radius: 1rem; height: 100%; transition: transform .15s, box-shadow .15s; }
        .kartu-master:hover { transform: translateY(-4px); box-shadow: 0 14px 30px rgba(13,155,224,.2); }
        .kartu-master .emoji { font-size: 2.4rem; display: inline-block; transform: rotate(-8deg); }
        .kartu-master .angka { font-size: 1.6rem; font-weight: 800; }
    </style>
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Master Data', 'crumb' => 'Master Data'])

                <div class="row g-3">
                    @foreach ($kartu as [$emoji, $nama, $rute, $total, $ket, $boleh])
                        @if ($boleh)
                            <div class="col-sm-6 col-xl-4">
                                <a href="{{ route($rute) }}" class="text-decoration-none">
                                    <div class="card kartu-master">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <span class="emoji" aria-hidden="true">{{ $emoji }}</span>
                                            <div>
                                                <div class="fw-bold">{{ $nama }}</div>
                                                <div class="small text-muted">{{ $ket }}</div>
                                                <div class="angka">{{ $total }} <span class="small fw-normal text-muted">data</span></div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
