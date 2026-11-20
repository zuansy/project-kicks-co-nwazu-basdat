@extends('layouts.app')

@section('title', 'Catat Retur')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Catat Retur', 'crumb' => 'Pilih Penerimaan'])

                <div class="card">
                    <div class="card-body">
                        <p class="text-muted">Pilih penerimaan yang barangnya mau dikembalikan ke vendor.</p>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Pengadaan</th>
                                        <th>Vendor</th>
                                        <th>Status</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($daftar as $r)
                                        <tr>
                                            <th scope="row">#{{ $r->idpenerimaan }}</th>
                                            <td>{{ \Illuminate\Support\Carbon::parse($r->waktu)->format('d/m/Y H:i') }}</td>
                                            <td>#{{ $r->idpengadaan }}</td>
                                            <td>{{ $r->nama_vendor }}</td>
                                            <td>@include('partials.status-penerimaan', ['status' => $r->status])</td>
                                            <td class="text-end">
                                                <a href="{{ route('retur.create', ['penerimaan' => $r->idpenerimaan]) }}" class="btn btn-sm btn-danger">Retur</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Belum ada penerimaan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
