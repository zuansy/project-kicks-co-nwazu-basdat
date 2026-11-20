@extends('layouts.app')

@section('title', 'Penjualan')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Penjualan', 'crumb' => 'Penjualan'])

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('penjualan.create') }}" class="btn btn-primary">+ Catat Penjualan</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Kasir</th>
                                        <th class="text-end">Margin</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($penjualan as $j)
                                        <tr>
                                            <th scope="row">#{{ $j->idpenjualan }}</th>
                                            <td>{{ \Illuminate\Support\Carbon::parse($j->waktu)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $j->username }}</td>
                                            <td class="text-end">{{ rtrim(rtrim(number_format($j->persen, 2, ',', '.'), '0'), ',') }}%</td>
                                            <td class="text-end">Rp {{ number_format($j->total_nilai, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('penjualan.show', $j->idpenjualan) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Belum ada penjualan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $penjualan->links() }}</div>
            </div>
        </div>
    </div>
@endsection
