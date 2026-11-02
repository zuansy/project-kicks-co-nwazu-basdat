@extends('layouts.app')

@section('title', 'Detail Pengadaan #' . $pengadaan->idpengadaan)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xl-9">
                @include('layouts.title', ['judul' => 'Pengadaan #' . $pengadaan->idpengadaan, 'crumb' => 'Detail'])

                <div class="card mb-4">
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">Vendor</dt>
                            <dd class="col-sm-9">{{ $pengadaan->nama_vendor }}</dd>
                            <dt class="col-sm-3">Waktu</dt>
                            <dd class="col-sm-9">{{ \Illuminate\Support\Carbon::parse($pengadaan->waktu)->format('d/m/Y H:i') }}</dd>
                            <dt class="col-sm-3">Dicatat oleh</dt>
                            <dd class="col-sm-9">{{ $pengadaan->username }}</dd>
                            <dt class="col-sm-3">Status</dt>
                            <dd class="col-sm-9">
                                @if ($selesai)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Menunggu barang</span>
                                @endif
                            </dd>
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
                                        <th class="text-end">Jumlah</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($detail as $d)
                                        <tr>
                                            <td>{{ $d->nama }}</td>
                                            <td class="text-end">{{ $d->jumlah }}</td>
                                            <td class="text-end">Rp {{ number_format($d->harga_satuan, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp {{ number_format($d->sub_total, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-end">Subtotal</td>
                                        <td class="text-end">Rp {{ number_format($pengadaan->subtotal_nilai, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end">PPN</td>
                                        <td class="text-end">Rp {{ number_format($pengadaan->ppn, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th class="text-end">Rp {{ number_format($pengadaan->total_nilai, 0, ',', '.') }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('pengadaan.index') }}" class="btn btn-outline-secondary">Kembali</a>
                    @unless ($selesai)
                        <a href="{{ route('penerimaan.create', ['pengadaan' => $pengadaan->idpengadaan]) }}"
                            class="btn btn-success">Terima Barang</a>
                    @endunless
                </div>
            </div>
        </div>
    </div>
@endsection
