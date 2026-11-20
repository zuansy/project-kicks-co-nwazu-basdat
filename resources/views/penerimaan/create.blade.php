@extends('layouts.app')

@section('title', 'Terima Barang')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-10">
                @include('layouts.title', ['judul' => 'Terima Barang — Pengadaan #' . $pengadaan->idpengadaan, 'crumb' => 'Terima'])

                <form action="{{ route('penerimaan.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="idpengadaan" value="{{ $pengadaan->idpengadaan }}">

                    <div class="card mb-4">
                        <div class="card-body">
                            <p class="mb-1 text-muted">Vendor: <strong>{{ $pengadaan->nama_vendor }}</strong></p>
                            <p class="small text-muted">Isi manual jumlah yang benar-benar datang. Kalau kurang dari sisa pesanan, penerimaan berstatus <strong>sebagian</strong> dan pengadaan tetap <strong>proses</strong> sampai sisanya diterima. Harga satuan terima terisi dari pengadaan dan boleh diubah.</p>
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th class="text-end">Dipesan</th>
                                            <th class="text-end">Sisa</th>
                                            <th style="width: 140px">Diterima sekarang</th>
                                            <th style="width: 190px">Harga satuan terima (Rp)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($detail as $d)
                                            @php $sisa = (int) $d->sisa; @endphp
                                            <tr>
                                                <td>{{ $d->nama }}</td>
                                                <td class="text-end">{{ $d->jumlah }}</td>
                                                <td class="text-end">{{ $sisa }}</td>
                                                <td>
                                                    <input type="number" min="0" max="{{ $sisa }}" name="terima[{{ $d->idbarang }}]"
                                                        value="{{ old('terima.' . $d->idbarang, $sisa) }}" class="form-control" @disabled($sisa <= 0)>
                                                </td>
                                                <td>
                                                    <input type="number" min="0" name="harga[{{ $d->idbarang }}]"
                                                        value="{{ old('harga.' . $d->idbarang, $d->harga_satuan) }}" class="form-control" @disabled($sisa <= 0)>
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
                        <button type="submit" class="btn btn-success">Catat Penerimaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
