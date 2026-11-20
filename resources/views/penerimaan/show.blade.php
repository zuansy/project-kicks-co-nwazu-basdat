@extends('layouts.app')

@section('title', 'Detail Penerimaan #' . $penerimaan->idpenerimaan)

@section('content')
    @php $pengadaanProses = $penerimaan->status_pengadaan === config('inventori.status_pengadaan_proses'); @endphp
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Penerimaan #' . $penerimaan->idpenerimaan, 'crumb' => 'Detail'])

                <div class="card mb-4">
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Pengadaan</dt>
                            <dd class="col-sm-9">
                                <a href="{{ route('pengadaan.show', $penerimaan->idpengadaan) }}">#{{ $penerimaan->idpengadaan }}</a>
                                &middot; {{ $penerimaan->nama_vendor }}
                                @include('partials.status-pengadaan', ['status' => $penerimaan->status_pengadaan])
                            </dd>
                            <dt class="col-sm-3">Waktu</dt>
                            <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($penerimaan->waktu)->format('d/m/Y H:i') }}</dd>
                            <dt class="col-sm-3">Dicatat oleh</dt>
                            <dd class="col-sm-9">{{ $penerimaan->username }}</dd>
                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">@include('partials.status-penerimaan', ['status' => $penerimaan->status])</dd>
                        </dl>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th class="text-end">Diterima</th>
                                        <th class="text-end">Diretur</th>
                                        <th class="text-end">Harga Satuan Terima</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td>{{ $d->nama }}</td>
                                            <td class="text-end">{{ $d->jumlah_terima }}</td>
                                            <td class="text-end">{{ $d->diretur }}</td>
                                            <td class="text-end">Rp {{ number_format($d->harga_satuan_terima, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($d->sub_total_terima, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('penerimaan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    @if ($pengadaanProses)
                        <a href="{{ route('penerimaan.create', ['pengadaan' => $penerimaan->idpengadaan]) }}" class="btn btn-success">Terima Sisa Barang</a>
                    @endif
                    <a href="{{ route('retur.create', ['penerimaan' => $penerimaan->idpenerimaan]) }}" class="btn btn-outline-danger">Retur Barang</a>
                </div>
            </div>
        </div>
    </div>
@endsection
