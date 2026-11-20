@extends('layouts.app')

@section('title', 'Catat Retur')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-10">
                @include('layouts.title', ['judul' => 'Retur — Penerimaan #' . $penerimaan->idpenerimaan, 'crumb' => 'Catat'])

                <form action="{{ route('retur.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="idpenerimaan" value="{{ $penerimaan->idpenerimaan }}">

                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Vendor: <strong>{{ $penerimaan->nama_vendor }}</strong></p>
                            <p class="small text-muted">Isi jumlah yang dikembalikan dan alasannya. Stok akan berkurang. Barang yang diretur dianggap kurang, jadi pengadaan yang sudah selesai kembali berstatus <strong>proses</strong> sampai vendor mengirim penggantinya.</p>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th class="text-end">Diterima</th>
                                            <th class="text-end">Sudah diretur</th>
                                            <th style="width: 130px">Retur sekarang</th>
                                            <th style="min-width: 240px">Alasan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail as $d)
                                            @php $maks = (int) $d->jumlah_terima - (int) $d->sudah_retur; @endphp
                                            <tr>
                                                <td>{{ $d->nama }}</td>
                                                <td class="text-end">{{ $d->jumlah_terima }}</td>
                                                <td class="text-end">{{ $d->sudah_retur }}</td>
                                                <td>
                                                    <input type="number" min="0" max="{{ $maks }}" name="retur[{{ $d->iddetail_penerimaan }}]"
                                                        value="{{ old('retur.' . $d->iddetail_penerimaan, 0) }}" class="form-control" @disabled($maks <= 0)>
                                                </td>
                                                <td>
                                                    <input type="text" maxlength="200" name="alasan[{{ $d->iddetail_penerimaan }}]"
                                                        value="{{ old('alasan.' . $d->iddetail_penerimaan) }}" class="form-control"
                                                        placeholder="mis. cacat / salah ukuran" @disabled($maks <= 0)>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('retur.create') }}" class="btn btn-outline-secondary">Kembali</a>
                        <button type="submit" class="btn btn-danger">Catat Retur</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
