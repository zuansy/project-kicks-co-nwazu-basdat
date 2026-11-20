@extends('layouts.app')

@section('title', 'Detail Retur #' . $retur->idretur)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Retur #' . $retur->idretur, 'crumb' => 'Detail'])

                <div class="card mb-4">
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Penerimaan</dt>
                            <dd class="col-sm-9"><a href="{{ route('penerimaan.show', $retur->idpenerimaan) }}">#{{ $retur->idpenerimaan }}</a> &middot; {{ $retur->nama_vendor }}</dd>
                            <dt class="col-sm-3">Pengadaan</dt>
                            <dd class="col-sm-9">
                                <a href="{{ route('pengadaan.show', $retur->idpengadaan) }}">#{{ $retur->idpengadaan }}</a>
                                @include('partials.status-pengadaan', ['status' => $retur->status_pengadaan])
                            </dd>
                            <dt class="col-sm-3">Waktu</dt>
                            <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($retur->waktu)->format('d/m/Y H:i') }}</dd>
                            <dt class="col-sm-3">Dicatat oleh</dt>
                            <dd class="col-sm-9">{{ $retur->username }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr><th>Barang</th><th class="text-end">Jumlah diretur</th><th>Alasan</th></tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td>{{ $d->nama }}</td>
                                            <td class="text-end">{{ $d->jumlah }}</td>
                                            <td>{{ $d->alasan }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <a href="{{ route('retur.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </div>
@endsection
