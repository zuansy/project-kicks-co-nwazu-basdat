@extends('layouts.app')

@section('title', 'Penerimaan')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Penerimaan Barang', 'crumb' => 'Penerimaan'])

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('penerimaan.create') }}" class="btn btn-success">+ Terima Barang</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Pengadaan</th>
                                        <th>Vendor</th>
                                        <th>Dicatat oleh</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($penerimaan as $r)
                                        <tr>
                                            <th scope="row">#{{ $r->idpenerimaan }}</th>
                                            <td>{{ \Illuminate\Support\Carbon::parse($r->waktu)->format('d/m/Y H:i') }}</td>
                                            <td><a href="{{ route('pengadaan.show', $r->idpengadaan) }}">#{{ $r->idpengadaan }}</a></td>
                                            <td>{{ $r->nama_vendor }}</td>
                                            <td>{{ $r->username }}</td>
                                            <td>@include('partials.status-penerimaan', ['status' => $r->status])</td>
                                            <td class="text-end">
                                                <a href="{{ route('penerimaan.show', $r->idpenerimaan) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada penerimaan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $penerimaan->links() }}</div>
            </div>
        </div>
    </div>
@endsection
