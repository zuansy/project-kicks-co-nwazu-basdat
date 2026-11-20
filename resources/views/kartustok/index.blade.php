@extends('layouts.app')

@section('title', 'Kartu Stok')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Kartu Stok', 'crumb' => 'Kartu Stok'])

                <form method="GET" action="{{ route('kartustok.index') }}" class="d-flex flex-wrap gap-2 mb-3">
                    <select name="barang" class="form-select" style="max-width: 360px" onchange="this.form.submit()">
                        <option value="">Semua barang</option>
                        @foreach ($barang as $b)
                            <option value="{{ $b->idbarang }}" @selected((string) $idBarang === (string) $b->idbarang)>{{ $b->nama }}</option>
                        @endforeach
                    </select>
                    @if ($idBarang)
                        <a href="{{ route('kartustok.index') }}" class="btn btn-outline-secondary">Reset</a>
                    @endif
                </form>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>Barang</th>
                                        <th>Keterangan</th>
                                        <th class="text-end">Masuk</th>
                                        <th class="text-end">Keluar</th>
                                        <th class="text-end">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($riwayat as $r)
                                        @php
                                            $awal = (int) $r->idtransaksi === 0 && (int) $r->masuk === 0 && (int) $r->keluar === 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $r->waktu ? \Illuminate\Support\Carbon::parse($r->waktu)->format('d/m/Y H:i') : '—' }}</td>
                                            <td>{{ $r->nama }}</td>
                                            <td>
                                                @if ($awal)
                                                    <span class="badge bg-secondary">Saldo awal</span>
                                                @elseif ($r->jenis_transaksi === 'M')
                                                    <span class="badge bg-success">Penerimaan #{{ $r->idtransaksi }}</span>
                                                @elseif ($r->jenis_transaksi === 'J')
                                                    <span class="badge bg-primary">Penjualan #{{ $r->idtransaksi }}</span>
                                                @elseif ($r->jenis_transaksi === 'R')
                                                    <span class="badge bg-danger">Retur #{{ $r->idtransaksi }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">{{ $r->jenis_transaksi }} #{{ $r->idtransaksi }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end">{{ $r->masuk ?: '—' }}</td>
                                            <td class="text-end">{{ $r->keluar ?: '—' }}</td>
                                            <th class="text-end">{{ $r->stock }}</th>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Belum ada riwayat stok.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $riwayat->links() }}</div>
            </div>
        </div>
    </div>
@endsection
