@extends('layouts.app')

@section('title', 'Retur Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col">
                @include('layouts.title', ['judul' => 'Retur Barang', 'crumb' => 'Retur'])

                <div class="d-flex justify-content-end mb-3">
                    <a href="{{ route('retur.create') }}" class="btn btn-danger">+ Catat Retur</a>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Penerimaan</th>
                                        <th>Vendor</th>
                                        <th>Dicatat oleh</th>
                                        <th class="text-end">Jumlah diretur</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($retur as $r)
                                        <tr>
                                            <th scope="row">#{{ $r->idretur }}</th>
                                            <td>{{ \Illuminate\Support\Carbon::parse($r->waktu)->format('d/m/Y H:i') }}</td>
                                            <td><a href="{{ route('penerimaan.show', $r->idpenerimaan) }}">#{{ $r->idpenerimaan }}</a></td>
                                            <td>{{ $r->nama_vendor }}</td>
                                            <td>{{ $r->username }}</td>
                                            <td class="text-end">{{ $r->total_jumlah }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('retur.show', $r->idretur) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada retur.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-3">{{ $retur->links() }}</div>
            </div>
        </div>
    </div>
@endsection
