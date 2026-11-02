@extends('layouts.app')

@section('title', 'Terima Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Terima Barang — Pengadaan #' . $pengadaan->idpengadaan, 'crumb' => 'Terima'])

                <form action="{{ route('penerimaan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="idpengadaan" value="{{ $pengadaan->idpengadaan }}">

                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="mb-3 text-muted">Vendor: <strong>{{ $pengadaan->nama_vendor }}</strong></p>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th class="text-end">Dipesan</th>
                                            <th class="text-end">Sudah diterima</th>
                                            <th class="text-end">Sisa</th>
                                            <th style="width: 150px">Diterima sekarang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail as $d)
                                            @php $sisa = (int) $d->jumlah - (int) $d->diterima; @endphp
                                            <tr>
                                                <td>{{ $d->nama }}</td>
                                                <td class="text-end">{{ $d->jumlah }}</td>
                                                <td class="text-end">{{ $d->diterima }}</td>
                                                <td class="text-end">{{ $sisa }}</td>
                                                <td>
                                                    <input type="number" min="0" max="{{ $sisa }}"
                                                        name="terima[{{ $d->idbarang }}]"
                                                        value="{{ old('terima.' . $d->idbarang, $sisa) }}"
                                                        class="form-control" @disabled($sisa <= 0)>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('penerimaan.create') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-success">Proses Penerimaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
