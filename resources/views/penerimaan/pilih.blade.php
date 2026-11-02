@extends('layouts.app')

@section('title', 'Terima Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Terima Barang', 'crumb' => 'Pilih Pengadaan'])

                <div class="card">
                    <div class="card-body">
                        <p class="text-muted">Pilih pengadaan yang barangnya datang.</p>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Waktu</th>
                                        <th>Vendor</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($terbuka as $p)
                                        <tr>
                                            <th scope="row">#{{ $p->idpengadaan }}</th>
                                            <td>{{ \Illuminate\Support\Carbon::parse($p->waktu)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $p->nama_vendor }}</td>
                                            <td class="text-end">Rp {{ number_format($p->total_nilai, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                <a href="{{ route('penerimaan.create', ['pengadaan' => $p->idpengadaan]) }}"
                                                    class="btn btn-sm btn-success">Terima</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Tidak ada pengadaan yang menunggu barang.
                                            </td>
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
