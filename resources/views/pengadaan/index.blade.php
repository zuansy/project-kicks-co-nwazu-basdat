@extends('layouts.app')

@section('title', 'Pengadaan')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Pengadaan', 'crumb' => 'Pengadaan'])

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('pengadaan.create') }}" class="btn btn-primary">+ Catat Pengadaan</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Vendor</th>
                                        <th>Dicatat oleh</th>
                                        <th class="text-end">Total</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($pengadaan as $p)
                                        <tr>
                                            <th scope="row">#{{ $p->idpengadaan }}</th>
                                            <td>{{ \Illuminate\Support\Carbon::parse($p->waktu)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $p->nama_vendor }}</td>
                                            <td>{{ $p->username }}</td>
                                            <td class="text-end">Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</td>
                                            <td>@include('partials.status-pengadaan', ['status' => $p->status])</td>
                                            <td class="text-end text-nowrap">
                                                <a href="{{ route('pengadaan.show', $p->idpengadaan) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada pengadaan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $pengadaan->links() }}</div>
            </div>
        </div>
    </div>
@endsection
